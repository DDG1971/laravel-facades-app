<?php

namespace App\Http\Controllers;

use App\Models\Milling;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Status;
use App\Models\Thickness;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacadeType;
use App\Models\ColorCode;
use App\Models\ColorCatalog;
use App\Models\CoatingType;
use App\Models\Drilling;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CalculationMail;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{
    /**
     * Список заказов (для клиента или менеджера).
     */
    public function index()
    {
        $orders = Order::with([
            'customer',
            'status',
            'colorCatalog',
            'colorCode',
            'coatingType',
            'milling'
        ])->get();
        $statuses = Status::all();

        return view('orders.index', compact('orders','statuses'));
    }

    /**
     * Форма создания нового заказа.
     */
    public function create()
    {
        $customers = Customer::all();
        $statuses = Status::all();
        $facadeTypes = FacadeType::all();
        $colors = ColorCode::all();
        $colorCatalogs = ColorCatalog::all();
        $coatingTypes = CoatingType::all();
        $drillings = Drilling::all();
        $millings = Milling::all();
        $thicknesses = Thickness::ordered()->get();

        return view('orders.create', compact(
            'customers',
            'statuses',
            'facadeTypes',
            'colors',
            'colorCatalogs',
            'coatingTypes',
            'drillings',
            'millings',
            'thicknesses'
        ));
    }

    public function createClient()
    {
        $customer = auth()->user()->customer; // только свой клиент
        $facadeTypes = FacadeType::all();
        $colors = ColorCode::all();
        $colorCatalogs = ColorCatalog::all();
        $coatingTypes = CoatingType::all();
        $drillings = Drilling::all();
        $millings = Milling::all();
        $thicknesses = Thickness::ordered()->get();

        return view('client.create', compact(
            'customer',
            'facadeTypes',
            'colors',
            'colorCatalogs',
            'coatingTypes',
            'drillings',
            'millings',
            'thicknesses'
        ));
    }


    /**
     * Сохранение нового заказа и его позиций.
     */

    public function store(Request $request)
    {
        // Валидация
        $rules = [
            'client_order_number' => 'nullable|string|max:50',
            'date_received' => 'nullable|date',
            'material' => 'required|string',
            'color_catalog_id' => 'nullable|exists:color_catalogs,id',
            'color_code_id' => ['required', 'exists:color_codes,id',
                function ($attribute, $value, $fail) use ($request) {
                    $catalogId = $request->input('color_catalog_id');
                    if (!\App\Models\ColorCode::where('id', $value)
                        ->where('color_catalog_id', $catalogId)->exists()) {
                        $fail('Выбранный цвет не принадлежит указанному каталогу.');
                    }
                },
            ],
            'coating_type_id' => 'nullable|exists:coating_types,id',
            'milling_id' => 'nullable|exists:millings,id',

            // Позиции
            'items.*.height' => 'nullable|integer|min:1',
            'items.*.width' => 'nullable|integer|min:1',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.thickness_id' => 'required|exists:thicknesses,id',
            'items.*.coating_mode' => 'nullable|integer|in:0,1,2',
            'items.*.facade_type_id' => 'nullable|exists:facade_types,id',
            'items.*.drilling_id' => 'nullable|exists:drillings,id',
            'items.*.notes' => 'nullable|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.total_price' => 'nullable|numeric|min:0',
            'order_attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
            'items.*.attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
        ];

        // 🔹 Если админ — добавляем проверку customer_id
        if (auth()->user()->role === 'admin') {
            $rules['customer_id'] = 'required|exists:customers,id';
        }
        // 🔹 Валидация
        $request->validate($rules);
        // 🔹 Определяем customer_id
        $customerId = auth()->user()->role === 'customer'
            ? auth()->user()->customer_id
            : $request->input('customer_id');

        // Статус по умолчанию
        $defaultStatusId = optional(Status::firstWhere('name', 'new'))->id
            ?? Status::min('id');

        $clientOrderNumber = trim($request->input('client_order_number') ?? '');
        $today = now()->toDateString();

        $clientOrderNumber = $clientOrderNumber === '' ? null : $clientOrderNumber;

        // Проверка на дубликат
        $duplicateFound = false;
        if ($clientOrderNumber) {
            $duplicateFound = Order::where('customer_id', $customerId)
                ->whereYear('date_received', Carbon::parse($today)->year)
                ->where('client_order_number', $clientOrderNumber)
                ->exists();
        }

        $order = DB::transaction(function () use (
            $defaultStatusId,
            $clientOrderNumber,
            $today,
            $customerId,
            $request
        ) {
            $lastQueue = Order::lockForUpdate()->max('queue_number') ?? 0;
            $queueNumber = $lastQueue + 1;

            return Order::create([
                'customer_id' => $customerId,
                'user_id' => Auth::id(),
                'status_id' => $defaultStatusId,
                'date_received' => $today,
                'queue_number' => $queueNumber,
                'client_order_number' => $clientOrderNumber ?? ('б/н-' . $queueNumber),
                'material' => $request->input('material'),
                'color_catalog_id' => $request->input('color_catalog_id'),
                'color_code_id' => $request->input('color_code_id'),
                'coating_type_id' => $request->input('coating_type_id'),
                'milling_id' => $request->input('milling_id'),
            ]);
        });

        // Вложение для заказа
        if ($request->hasFile('order_attachment')) {
            $file = $request->file('order_attachment');
            $path = $file->store('orders/attachments', 'public');
            //dd($path);
            $order->attachment_path = $path;
            $order->save();
        }
        // Позиции
        foreach ($request->input('items', []) as $index => $item) {
            if (empty($item['height']) && empty($item['width']) && empty($item['quantity'])) {
                continue;
            }
            $attachmentPath = null;
            //  Проверяю файл по индексу
             if ($request->hasFile("items.$index.attachment")) {
                 $file = $request->file("items.$index.attachment");
                 $attachmentPath = $file->store('order_items/attachments', 'public');
             }


            OrderItem::create([
                'order_id' => $order->id,
                'facade_type_id' => $item['facade_type_id'] ?? null,
                'thickness_id' => $item['thickness_id'],
                'height' => $item['height'],
                'width' => $item['width'],
                'square_meters' => $item['square_meters'] ?? null,
                'quantity' => $item['quantity'],
                'coating_mode' => $item['coating_mode'] ?? 0,
                'drilling_id' => $item['drilling_id'] ?? null,
                'notes' => $item['notes'] ?? null,
                'unit_price' => $item['unit_price'] ?? 0,
                'total_price' => $item['total_price'] ?? 0,
                'date_created' => now(),
                'attachment_path' => $attachmentPath,
            ]);
        }

        // Пересчёт итогов
        $order->load('items');

        $totalSquare = 0;
        $totalPrice = 0;

        foreach ($order->items as $item) {
            $square = $item->square_meters
                ?? (($item->height * $item->width / 1_000_000) * $item->quantity);

            $totalSquare += $square;
            $totalPrice += $item->total_price ?? 0;
        }

        $order->update([
            'square_meters' => $totalSquare,
            'total_price' => $totalPrice,
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('duplicate', $duplicateFound)
            ->with('success', 'Заказ создан!');
    }


    /**
     * Просмотр заказа.
     */
    public function show(Order $order)
    {
        $order->load(
            'items',
            'customer',
            'status',
            'colorCatalog',
            'colorCode',
            'coatingType',
            'milling'
        );

        return view('orders.show', compact('order'));
    }

    /**
     * Редактирование заказа.
     */
    public function edit(Order $order)
    {
        $order->load('items.thickness');

        $customers = Customer::all();
        $statuses = Status::all();
        $colorCatalogs = ColorCatalog::all();
        $colors = ColorCode::all();
        $coatingTypes = CoatingType::all();
        $millings = Milling::all();
        $facadeTypes = FacadeType::all();
        $drillings = Drilling::all();
        $thicknesses = Thickness::orderByRaw("
         CASE value
           WHEN 19 THEN 1
           WHEN 22 THEN 2
           WHEN 16 THEN 3
           WHEN 38 THEN 4
           ELSE 5
            END
         ")->orderBy('value')->get();

        return view('orders.edit', compact(
            'order',
            'customers',
            'statuses',
            'colorCatalogs',
            'colors',
            'coatingTypes',
            'millings',
            'facadeTypes',
            'drillings',
            'thicknesses'
        ));
    }

    /**
     * Обновление заказа и его позиций.
     */
    public function update(Request $request, Order $order)
    {
        // Валидация
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'client_order_number' => 'nullable|string|max:50',
            'date_received' => 'nullable|date',
            'material' => 'required|string',
            'color_catalog_id' => 'nullable|exists:color_catalogs,id',
            'color_code_id' => ['required', 'exists:color_codes,id',
                function ($attribute, $value, $fail) use ($request) {
                    $catalogId = $request->input('color_catalog_id');
                    if (!\App\Models\ColorCode::where('id', $value)
                        ->where('color_catalog_id', $catalogId)->exists()) {
                        $fail('Выбранный цвет не принадлежит указанному каталогу.');
                    }
                },
            ],
            'coating_type_id' => 'nullable|exists:coating_types,id',
            'milling_id' => 'nullable|exists:millings,id',

            // Позиции
            'items.*.height' => 'required|integer|min:1',
            'items.*.width' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.thickness_id' => 'required|exists:thicknesses,id',
            'items.*.coating_mode' => 'nullable|integer|in:0,1,2',
            'items.*.facade_type_id' => 'nullable|exists:facade_types,id',
            'items.*.drilling_id' => 'nullable|exists:drillings,id',
            'items.*.notes' => 'nullable|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.total_price' => 'nullable|numeric|min:0',
            'order_attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
            'items.*.attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
        ]);

        // Данные «шапки» заказа
        $orderData = [
            'customer_id' => $validated['customer_id'],
            'status_id' => $order->status_id,
            'client_order_number' => $request->input('client_order_number'),
            'color_catalog_id' => $request->input('color_catalog_id'),
            'color_code_id' => $request->input('color_code_id'),
            'coating_type_id' => $request->input('coating_type_id'),
            'material' => $request->input('material'),
            'milling_id' => $request->input('milling_id'),
        ];

        if ($request->hasFile('order_attachment')) {
            $orderData['attachment_path'] = $request->file('order_attachment')
                ->store('orders/attachments', 'public');
        }

        $order->update($orderData);

        // Позиции
        $items = $request->input('items', []);
        unset($items['__INDEX__']);

        foreach ($items as $itemData) {
            if (empty($itemData['height']) && empty($itemData['width']) && empty($itemData['quantity'])) {
                continue;
            }

            $attachmentPath = null;
            if (isset($itemData['attachment']) && $itemData['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $attachmentPath = $itemData['attachment']->store('order_items/attachments', 'public');
            }

            if (!empty($itemData['id'])) {
                // обновление существующей позиции
                $item = OrderItem::find($itemData['id']);
                if ($item) {
                    $item->update([
                        'facade_type_id' => $itemData['facade_type_id'] ?? null,
                        'thickness_id' => $itemData['thickness_id'],
                        'height' => $itemData['height'],
                        'width' => $itemData['width'],
                        'square_meters' => $itemData['square_meters'] ?? null,
                        'quantity' => $itemData['quantity'],
                        'coating_mode' => $itemData['coating_mode'] ?? 0,
                        'drilling_id' => $itemData['drilling_id'] ?? null,
                        'notes' => $itemData['notes'] ?? null,
                        'unit_price' => $itemData['unit_price'] ?? 0,
                        'total_price' => $itemData['total_price'] ?? 0,
                        'attachment_path' => $attachmentPath ?? $item->attachment_path,
                    ]);
                }
            } else {
                // создание новой позиции
                $order->items()->create([
                    'facade_type_id' => $itemData['facade_type_id'] ?? null,
                    'thickness_id' => $itemData['thickness_id'],
                    'height' => $itemData['height'],
                    'width' => $itemData['width'],
                    'square_meters' => $itemData['square_meters'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'coating_mode' => $itemData['coating_mode'] ?? 0,
                    'drilling_id' => $itemData['drilling_id'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'total_price' => $itemData['total_price'] ?? 0,
                    'attachment_path' => $attachmentPath,
                ]);
            }
        }

        // Пересчёт итогов
        $order->load('items');
        $totalSquare = $order->items->sum(fn($item) => $item->square_meters ?? (($item->height * $item->width / 1_000_000) * $item->quantity)
        );
        $totalPrice = $order->items->sum('total_price');

        $order->update([
            'square_meters' => $totalSquare,
            'total_price' => $totalPrice,
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Заказ успешно обновлён');
    }

    public function indexClient()
    {
        $customer = auth()->user()->customer;
        // Загружаем только заказы этого клиента
        $orders = Order::where('customer_id', $customer->id)
            ->with(['status', 'colorCatalog', 'colorCode', 'coatingType', 'milling'])
            ->orderByDesc('date_received')
            ->get();

        return view('client.index', compact('orders', 'customer'));
    }


    /**
     * Подтверждение заказа клиентом.
     */
    public function submit(Order $order)
    {
        $order->update([
            'date_status' => now(),
        ]);
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.orders.index')
                ->with('success', 'Заказ отправлен!');
        }
        if ($user->hasRole('customer')) {
            return redirect()->route('orders.index')
                ->with('success', 'Заказ отправлен!');
        }
        if ($user->hasRole('manager')) {
            return redirect()->route('manager.dashboard')
                ->with('success', 'Заказ отправлен!');
        }
        return redirect()->route('dashboard')
            ->with('success', 'Заказ отправлен!');
    }

    public function preview(Order $order)
    {
        $items = $order->items;
        $totalQuantity = $items->sum('quantity');
        $totalSquare = $items->sum('square_meters');

        return view('orders.preview', compact('order', 'items', 'totalQuantity', 'totalSquare'));
    }

    /**
     * Удаление позиции из заказа.
     */

    public function destroyItem(OrderItem $item)
    {
        $item->delete();

        return back();
    }

    /**
     * На пилу.
     */
    public function saw(Order $order)
    {
        $items = $order->items()->with(['milling', 'facadeType'])->get();

        return view('orders.saw', compact('order', 'items'));
    }

    public function manage(Order $order, Request $request)
    {
        $priceGroup = $request->input('price_group', 'retail');

        $allowed = ['retail', 'dealer', 'private'];
        if (!in_array($priceGroup, $allowed, true)) {
            $priceGroup = 'retail';  // дефолт
        }

        $customers = Customer::all();
        $colorCatalogs = ColorCatalog::all();
        $colors = ColorCode::all();
        $coatingTypes = CoatingType::all();
        $millings = Milling::all();
        $facadeTypes = FacadeType::all();
        $thicknesses = Thickness::all();
        $drillings = Drilling::all();
        $statuses = Status::all();

        return view('orders.manage', compact(
            'order',
            'customers',
            'colorCatalogs',
            'colors',
            'coatingTypes',
            'millings',
            'facadeTypes',
            'thicknesses',
            'drillings',
            'statuses',
            'priceGroup',
        ));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $order->status_id = $request->input('status_id');
        $order->date_status = now();
        $order->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $order->status->label, // русский текст
                'status_key' => $order->status->name, //  технический ключ
                'date_status' => $order->date_status->format('d.m.Y'),
            ]);
        }
        return redirect()->back()->with('success', 'Статус заказа обновлён.');

    }


    public function sendToClient(Order $order, Request $request)
    {
        $priceGroup = $request->input('price_group', 'retail');
        // или можно брать из $order->customer->price_group
        return view('orders.partials.calculation-client', compact('order', 'priceGroup'));
    }

   public function exportClientPdf(Order $order, Request $request)
    {
        $priceGroup = $request->input('price_group', 'retail');
        $allowed = ['retail', 'dealer', 'private'];
        if (!in_array($priceGroup, $allowed, true)) {
            $priceGroup = 'retail';
        }
        //  генериру PDF
        $pdf = Pdf::loadView('orders.pdf.calculation-client', [
            'order' => $order,
            'priceGroup' => $priceGroup,
            ]);
        $path = storage_path("app/public/order-{$order->queue_number}-calculation.pdf");
        $pdf->save($path);

        return response()->download($path)->deleteFileAfterSend();
    }

    public function sendCalculation(Order $order, Request $request)
    {  //$pdf = Pdf::loadHTML('<h1>Тестовый PDF</h1>');
        // $recipient = $order->customer?->email ?? 'test@example.com'; Mail::raw('Во вложении расчёт заказа', function ($message) use ($order, $pdf, $recipient) { $message->to($recipient) ->subject("Расчёт заказа №{$order->id}") ->attachData($pdf->output(), "order-{$order->id}-calculation.pdf"); }); return back()->with('success', "Расчёт отправлен на {$recipient}!");
        //Mail::raw('Тестовое письмо', function ($message) { $message->to('test@example.com') ->subject('MailHog test'); });
        $order->load([
            'user',
            'customer',
            'colorCatalog',
            'colorCode',
            'coatingType',
            'milling',
            'items.facadeType',
            'items.thickness'
        ]);

        $priceGroup = $request->input('price_group', 'retail');
        $allowed = ['retail', 'dealer', 'private'];
        if (!in_array($priceGroup, $allowed, true)) {
            $priceGroup = 'retail';
        }

        $recipient = $order->user?->email;

        if (empty($recipient)) {
            return back()->with('error', 'У клиента не найден ни один пользователь с email.');
        }
        ///Mail::send(new CalculationMail($order, $priceGroup));
      // Mail::to($recipient)->send(new CalculationMail($order, $priceGroup));
        Mail::to($recipient)->queue(new CalculationMail($order, $priceGroup));


        return back()->with('success', "Расчёт отправлен на {$recipient}!");

    }

}
