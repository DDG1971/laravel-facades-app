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
        // Подгружаем нужные связи прямо внутри Job
        $this->order->load(['status', 'user', 'colorCode', 'coatingType', 'milling', 'items']);

        $chatId = $this->order->user->telegram_chat_id ?? null;

        if ($chatId) {
            $colorFull = "RAL" . ($this->order->colorCode->code ?? '???') . " " . ($this->order->coatingType->name ?? '');
            $totalQty = $this->order->items->sum('quantity');

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "🔔 **Статус заказа изменен!**\n\n" .
                    "✅ **Новый статус:** *{$this->order->status->label}*\n" .
                    "🚀 **Заказ #{$this->order->queue_number}**\n" .
                    "📑 **Ваш док.:** `" . ($this->order->client_order_number ?: 'б/н') . "`\n\n" .
                    "🎨 **Спецификация:**\n" .
                    "📦 **Материал:** {$this->order->material}\n" .
                    "🌈 **Цвет:** `{$colorFull}`\n" .
                    "🪵 **Фрезеровка:** " . ($this->order->milling->name ?? 'Не указана') . "\n\n" .
                    "📊 **Итого:**\n" .
                    "🔢 **Кол-во:** {$totalQty} шт.\n" .
                    "📐 **Площадь:** " . number_format($this->order->total_square, 2) . " м²",
                'parse_mode' => 'Markdown'
            ]);
        }
    }
}
