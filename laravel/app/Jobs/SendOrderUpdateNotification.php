<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendOrderUpdateNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        // Передаем модель заказа в Job
        $this->order = $order;
    }

    public function handle()
    {
        // 1. Подгружаем связи
        $this->order->load(['status', 'user', 'customer', 'colorCode.colorCatalog', 'coatingType', 'milling', 'items']);

        $status = $this->order->status;

        // 2. Исключаем "В покраске"
        if ($status->name === 'paint_shop') {
            return;
        }

        // 3. ФОРМИРУЕМ ТЕКСТ (теперь он прямо здесь)
        $catalogName = $this->order->colorCode->colorCatalog->name ?? 'Цвет';
        $colorFull = "{$catalogName} " . ($this->order->colorCode->code ?? '???') . " " . ($this->order->coatingType->name ?? '');
        $totalQty = $this->order->items->sum('quantity');

        $text = "🔔 **Статус заказа изменен!**\n\n" .
            "✅ **Новый статус:** *{$status->label}*\n" .
            "🚀 **Заказ #{$this->order->queue_number}**\n" .
            "📑 **Ваш док.:** `" . ($this->order->client_order_number ?: 'б/н') . "`\n\n" .
            "🎨 **Спецификация:**\n" .
            "📦 **Материал:** {$this->order->material}\n" .
            "🌈 **Цвет:** `{$colorFull}`\n" .
            "🪵 **Фрезеровка:** " . ($this->order->milling->name ?? 'Не указана') . "\n\n" .
            "📊 **Итого:**\n" .
            "🔢 **Кол-во:** {$totalQty} шт.\n" .
            "📐 **Площадь:** " . number_format($this->order->total_square, 2) . " м²";

        // 4. Отправка Менеджеру (если есть ID и стоит ГАЛОЧКА)
        if ($this->order->user->telegram_chat_id && $this->order->user->notify_manager_tg) {
            $this->dispatchTelegram($this->order->user->telegram_chat_id, $text);
        }

        // 5. Отправка Руководителю (если есть ID и стоит ГАЛОЧКА)
        if ($this->order->customer->telegram_chat_id && $this->order->customer->notify_owner_tg) {
            $this->dispatchTelegram($this->order->customer->telegram_chat_id, $text);
        }
    }

    /**
     * Не забудь добавить этот вспомогательный метод в конец класса Job!
     */
    private function dispatchTelegram($chatId, $text)
    {
        try {
            \Telegram\Bot\Laravel\Facades\Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            \Log::error("TG Job Error: " . $e->getMessage());
        }
    }

    /**
     * Вспомогательный метод внутри Job для чистоты кода
     */
   /** private function sendToTelegram($chatId, $text)
    {
        try {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            \Log::error("Ошибка Job Telegram для ID {$chatId}: " . $e->getMessage());
        }
    }*/

}
