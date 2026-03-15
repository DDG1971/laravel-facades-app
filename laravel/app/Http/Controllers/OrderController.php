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
use App\Mail\OrderReceivedMail;
use Telegram\Bot\Laravel\Facades\Telegram;


class OrderController extends Controller
{
    /**
     * Список заказов (для клиента или менеджера).
     */
    public function index(Request $request)
    {
        // запрос
        $query = Order::query()->with([
            'customer:id,company_name', // Берем только нужные поля
            'status',
            'colorCatalog',
            'colorCode',
            'coatingType',
            'milling'
        ]);

        // Поиск по номеру заказа клиента (LIKE для частичного совпадения)
        if ($request->filled('client_order_number')) {
            $query->where('client_order_number', 'like', '%' . $request->client_order_number . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('color_code_id')) {
            $query->where('color_code_id', $request->color_code_id);
        }
        // Поиск по фрезеровке
        if ($request->filled('milling_id')) {
            $query->where('milling_id', $request->milling_id);
        }
        // Исключаю заказы со статусом paint_shop
        $query->whereHas('status', function ($q) {
            $q->where('name', '!=', 'paint_shop');
        });

        // --- КОНЕЦ ФИЛЬТРОВ ---

        $orders = $query->orderByDesc('queue_number')->paginate(50);

        $customers = \App\Models\Customer::select('id', 'company_name')->orderBy('company_name')->get();
        $colorCodes = \App\Models\ColorCode::select('id', 'code')->orderBy('code')->get();
        $millings = \App\Models\Milling::orderBy('name')->get();
        $statuses = \App\Models\Status::where('name', '!=', 'paint_shop')->get();

        return view('orders.index', compact('orders', 'statuses', 'customers', 'millings', 'colorCodes'));
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
            'items.*.thickness_id' => 'nullable|exists:thicknesses,id',
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
        // 2. Подготовка данных перед транзакцией
        $customerId = auth()->user()->role === 'customer' ? auth()->user()->customer_id : $request->input('customer_id');
        $defaultStatusId = optional(Status::firstWhere('name', 'new'))->id ?? Status::min('id');
        $clientOrderNumber = trim($request->input('client_order_number') ?? '');
        $clientOrderNumber = $clientOrderNumber === '' ? null : $clientOrderNumber;
        $today = now()->toDateString();

        // Проверка на дубликат (до транзакции, чтобы не нагружать БД блокировками)
        $duplicateFound = false;
        if ($clientOrderNumber) {
            $duplicateFound = Order::where('customer_id', $customerId)
                ->whereYear('date_received', now()->year)
                ->where('client_order_number', $clientOrderNumber)
                ->exists();
        }

        // 3. Основная транзакция
        $order = DB::transaction(function () use ($request, $customerId, $defaultStatusId, $clientOrderNumber, $today) {

            // Блокируем только получение последнего номера
            $lastQueue = Order::orderBy('queue_number', 'desc')->lockForUpdate()->value('queue_number') ?? 0;
            $queueNumber = $lastQueue + 1;

            // Создаем сам заказ
            $order = Order::create([
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


            // Сохраняем вложение заказа (внутри транзакции)
            if ($request->hasFile('order_attachment')) {
                $order->update(['attachment_path' => $request->file('order_attachment')->store('orders/attachments', 'public')]);
            }

            $totalSquare = 0;
            $totalPrice = 0;

            // Создаем позиции заказа (внутри транзакции)
            foreach ($request->input('items', []) as $index => $itemData) {
                if (empty($itemData['height']) && empty($itemData['width']) && empty($itemData['quantity'])) {
                    continue;
                }

                $attachmentPath = $request->hasFile("items.$index.attachment")
                    ? $request->file("items.$index.attachment")->store('order_items/attachments', 'public')
                    : null;

                // Считаем площадь сразу для этой позиции
                $square = ($itemData['height'] * $itemData['width'] / 1_000_000) * $itemData['quantity'];

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'facade_type_id' => $itemData['facade_type_id'] ?? null,
                    'thickness_id' => !empty($itemData['thickness_id']) ? $itemData['thickness_id'] : null,
                    'height' => $itemData['height'],
                    'width' => $itemData['width'],
                    'square_meters' => $square,
                    'quantity' => $itemData['quantity'],
                    'coating_mode' => $itemData['coating_mode'] ?? 0,
                    'drilling_id' => $itemData['drilling_id'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'total_price' => $itemData['total_price'] ?? 0,
                    'date_created' => now(),
                    'attachment_path' => $attachmentPath,
                ]);

        // Вызываю  метод расчета, который  написан в модели
                $backendPrice = $orderItem->calculatePrice();

           // Обновляю запись в базе реальной ценой
                $orderItem->update([
                    'total_price' => $backendPrice,
                    'unit_price' => $orderItem->getRate()
                ]);

                $totalSquare += $square;
                $totalPrice += $backendPrice;
            }

            // Финальное обновление сумм заказа
            $order->update([
                'square_meters' => $totalSquare,
                'total_price' => $totalPrice,
            ]);
            // ТЕПЕРЬ ОТПРАВЛЯЕМ В ТЕЛЕГРАМ (когда всё посчитано)
            try {
                $order->load(['customer', 'user', 'colorCode.colorCatalog', 'coatingType', 'milling']);

                // 1. Собираем список получателей (Админ из .env + все Менеджеры с привязанным ТГ)
                $adminId = config('services.telegram.admin_id');
                $managerIds = \App\Models\User::where('role', 'manager') // убедись, что роль называется 'manager'
                ->whereNotNull('telegram_chat_id')
                    ->pluck('telegram_chat_id')
                    ->toArray();

                // Объединяем и убираем дубликаты
                $allRecipients = array_unique(array_filter(array_merge([$adminId], $managerIds)));

                // Подготовка данных сообщения (один раз)
                $companyName = $order->customer->company_name ?? 'Частное лицо';
                $managerPhone = $order->user->phone ?? 'нет тел.';
                $catalogName = $order->colorCode->colorCatalog->name ?? 'Цвет';
                $colorVal = $order->colorCode->code ?? '???';
                $coating = $order->coatingType->name ?? '';
                $colorFull = "{$catalogName} {$colorVal} {$coating}";
                $totalQty = collect($request->input('items', []))->sum('quantity');

                $messageText = "🚀 **Новый заказ #{$order->queue_number}**\n" .
                    "🏢 **Компания:** {$companyName}\n" .
                    "👤 **Менеджер:** {$order->user->name} (`{$managerPhone}`)\n" .
                    "📑 **Док. клиента:** `" . ($order->client_order_number ?: 'б/н') . "`\n\n" .
                    "🎨 **Спецификация:**\n" .
                    "📦 **Материал:** {$order->material}\n" .
                    "🌈 **Цвет:** `{$colorFull}`\n" .
                    "🪵 **Фрезеровка:**\n" . ($order->milling->name ?? 'Не указана') . "\n\n" .
                    "📊 **Итого:**\n" .
                    "🔢 **Кол-во:** {$totalQty} шт.\n" .
                    "📐 **Площадь:** " . number_format($totalSquare, 2) . " м²";

                // 2. РАССЫЛКА ВСЕМ ПОЛУЧАТЕЛЯМ
                foreach ($allRecipients as $chatId) {
                    \Telegram\Bot\Laravel\Facades\Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => $messageText,
                        'parse_mode' => 'Markdown'
                    ]);

                    // Отправляем файл чертежа, если он есть
                    if ($order->attachment_path) {
                        \Telegram\Bot\Laravel\Facades\Telegram::sendDocument([
                            'chat_id' => $chatId,
                            'document' => \Telegram\Bot\FileUpload\InputFile::create(
                                storage_path('app/public/' . $order->attachment_path),
                                'Чертеж_Заказа_#' . $order->queue_number . '.pdf'
                            ),
                            'caption' => "📎 Файл к заказу #{$order->queue_number}"
                        ]);
                    }
                }

            } catch (\Exception $e) {
                \Log::error("Ошибка телеграма: " . $e->getMessage());
            }

            return $order;
        }); // Конец транзакции DB::transaction

        // 4. Отправка уведомления (через ОЧЕРЕДЬ!)
        // Воркер подхватит это сразу после того, как БД сохранит транзакцию
        Mail::to($order->user->email)->queue(new OrderReceivedMail($order));

        return redirect()->route('orders.show', $order->id)
            ->with('duplicate', $duplicateFound)
            ->with('success', 'Заказ №' . $order->queue_number . ' успешно создан! Отправлено подтверждение. Проверьте чертежи перед отправкой расчета.');


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
            'items.*.thickness_id' => 'nullable|exists:thicknesses,id',
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
                        'thickness_id' => !empty($itemData['thickness_id']) ? $itemData['thickness_id'] : null, // Защита от пустой строки
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

    public function indexClient(Request $request)
    {
        $customer = auth()->user()->customer;
        if (!$customer) abort(403);

        $query = Order::where('customer_id', $customer->id)
            ->with(['status', 'colorCatalog', 'colorCode', 'coatingType', 'milling']);

        // --- ФИЛЬТРЫ ---
        if ($request->filled('client_order_number')) {
            $query->where('client_order_number', 'like', '%' . $request->client_order_number . '%');
        }

        if ($request->filled('color_code_id')) {
            $query->where('color_code_id', $request->color_code_id);
        }

        // НОВЫЙ ФИЛЬТР ПО СТАТУСУ
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $orders = $query->orderByDesc('queue_number')->paginate(30);

        // Данные для выпадающих списков
        $colorCodes = \App\Models\ColorCode::select('id', 'code')->orderBy('code')->get();
        $statuses = \App\Models\Status::where('name', '!=', 'paint_shop')->get();
        $millings = \App\Models\Milling::orderBy('name')->get();

        return view('client.index', compact('orders', 'customer', 'colorCodes', 'millings', 'statuses'));
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
        // Явно подгружаем всё, что нужно для шапки заказа
        $order->load(['customer', 'colorCatalog', 'colorCode', 'milling', 'coatingType']);

        $items = $order->items()->with(['facadeType', 'thickness', 'drilling'])->get();

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
        // 1. Загружаем только клиента для шапки
        $order->load(['customer']);

        // 2. Загружаем позиции БЕЗ связи milling (из-за которой была ошибка)
        $items = $order->items()
            ->with(['facadeType', 'thickness','order.milling'])
            ->get();

        $totalQuantity = $items->sum('quantity');

        // 3. Возвращаем ваш старый блейд без изменений
        return view('orders.saw', compact('order', 'items', 'totalQuantity'));
    }

    public function manage(Order $order, Request $request)
    {
        $order->load(['items.drilling', 'items.facadeType', 'items.thickness']);
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
        // 1. Обновляем данные в базе
        $order->update([
            'status_id' => $request->input('status_id'),
            'date_status' => now(),
        ]);

        // 2. Просто кидаем задачу в очередь.
        // Job сам внутри себя проверит статус, галочки и отправит ТГ.
        \App\Jobs\SendOrderUpdateNotification::dispatch($order);

        // 3. Ответ для AJAX (чтобы на фронте всё обновилось)
        if ($request->ajax()) {
            $order->load('status');
            return response()->json([
                'success' => true,
                'status' => $order->status->label,
                'status_key' => $order->status->name,
                'date_status' => $order->date_status->format('d.m.Y'),
            ]);
        }

        return redirect()->back()->with('success', 'Статус заказа обновлён.');
    }
    public function getStatusData(Order $order)
    {
        // Загружаем статус, чтобы получить label и name
        $order->load('status');

        return response()->json([
            'success' => true,
            'status_id'   => $order->status_id,          // Чтобы у менеджера переключился селект
            'status_key'  => $order->status->name,       // Чтобы перекрасилась строка
            'label'       => $order->status->label,      // Чтобы у клиента сменился текст
            'date_status' => $order->date_status ? $order->date_status->format('d.m.Y') : '—',
        ]);
    }


    public function sendToClient(Order $order, Request $request)
    {
        $priceGroup = $request->input('price_group', 'retail');
        // или можно брать из $order->customer->price_group
        return view('orders.partials.calculation-client', compact('order', 'priceGroup'));
    }

    public function exportClientPdf(Order $order, Request $request)
    {
        // 1. ПОДГРУЖАЕМ СВЯЗИ
        $order->load([
            'customer',
            'colorCatalog',
            'colorCode',
            'coatingType',
            'milling',
            'items.facadeType',
            'items.thickness',
            'items.drilling' // Самое важное для цены сверловки
        ]);

        $priceGroup = $request->input('price_group', 'retail');
        $allowed = ['retail', 'dealer', 'private'];
        if (!in_array($priceGroup, $allowed, true)) {
            $priceGroup = 'retail';
        }

        // 2. Генерируем PDF
        $pdf = Pdf::loadView('orders.pdf.calculation-client', [
            'order' => $order,
            'priceGroup' => $priceGroup,
        ]);

        $path = storage_path("app/public/order-{$order->queue_number}-calculation.pdf");
        $pdf->save($path);

        return response()->download($path)->deleteFileAfterSend();
    }


    public function sendCalculation(Order $order, Request $request)
    {
        $target = $request->input('target'); // Получаем, кто цель: manager или customer
        if ($target === 'manager') {
            // Обновляем только флаги менеджера
            $order->user->update([
                'notify_manager' => $request->has('notify_manager'),
                'notify_manager_tg' => $request->has('notify_manager_tg')
            ]);
            $statusMessage = "Расчёт отправлен Менеджеру.";
        }

        if ($target === 'customer') {
            // Обновляем только флаги клиента
            $order->customer->update([
                'notify_owner' => $request->has('notify_owner'),
                'notify_owner_tg' => $request->has('notify_owner_tg')
            ]);
            $statusMessage = "Расчёт отправлен Клиенту.";
        }

        // 2. Подгружаем связи
        $order->load(['user', 'customer', 'status', 'colorCode.colorCatalog', 'coatingType', 'milling', 'items']);

        // 3. Логика Email
        $recipients = [];
        if ($order->user->notify_manager && !empty($order->user->email)) $recipients[] = $order->user->email;
        if ($order->customer->notify_owner && !empty($order->customer->email)) $recipients[] = $order->customer->email;

        if (!empty($recipients)) {
            $priceGroup = in_array($request->input('price_group'), ['retail', 'dealer', 'private'])
                ? $request->input('price_group') : 'retail';
            Mail::to(array_unique($recipients))->queue(new CalculationMail($order, $priceGroup));
        }

        // 4. ЗАПУСК TELEGRAM (вынесли из-под условия Email)
        // Теперь ТГ уйдет, даже если Email пустой
        dispatch(new \App\Jobs\SendOrderUpdateNotification($order));

        $statusMessage = !empty($recipients)
            ? "Расчёт отправлен на Email (" . implode(', ', $recipients) . ") и в Telegram."
            : "Расчёт отправлен в Telegram.";

        return back()->with('success', $statusMessage);
    }

    public function updatePayment(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        // 1. Фиксируем итоговую цену СРАЗУ, если она 0, ДО сравнения
        if ($order->total_price <= 0) {
            $priceGroup = $request->input('price_group', 'retail');
            $order->total_price = $order->calculateTotal($priceGroup);
        }

        // 2. Прибавляем платеж
        $order->paid_amount += (float)$request->amount;

        // 3. Сравниваем (используем небольшую дельту для исключения ошибок плавающей точки)
        if ($order->paid_amount >= ($order->total_price - 0.01)) {
            $order->payment_status = 'paid';
        } elseif ($order->paid_amount > 0) {
            $order->payment_status = 'partial';
        } else {
            $order->payment_status = 'unpaid';
        }

        $order->save();

        return back()->with('success', 'Payment of $' . $request->amount . ' posted!');
    }

    private function dispatchTelegram($chatId, $text)
    {
        try {
            \Telegram\Bot\Laravel\Facades\Telegram::sendMessage([
                'chat_id' => $chatId,
                'text'    => $text,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            \Log::error("Ошибка отправки в Telegram: " . $e->getMessage());
        }
    }



}
