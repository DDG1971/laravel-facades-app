<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $update = Telegram::getWebhookUpdate();
            $message = $update->getMessage();

            if ($message && $message->has('text')) {
                $text = $message->getText();
                $chatId = $message->getChat()->getId();

                // Ищем команду /start с ID пользователя
                // Регулярка /start (\d+) выцепит только цифры после пробела
                if (preg_match('/\/start\s+(\d+)/', $text, $matches)) {
                    $userId = $matches[1];

                    $user = User::find($userId);
                    if ($user) {
                        // Привязываем ID. Ошибка Duplicate Entry не вылетит,
                        // если этот ID уже привязан к ЭТОМУ же юзеру.
                        $user->update(['telegram_chat_id' => $chatId]);

                        Telegram::sendMessage([
                            'chat_id' => $chatId,
                            'text' => "🤝 **Стиль Фасад: Приветствуем, {$user->name}!**\n\n✅ Уведомления по вашим заказам успешно активированы.",
                            'parse_mode' => 'Markdown'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Telegram Webhook Error: " . $e->getMessage());
        }

        // Telegram обязан получать 200 OK, иначе будет слать повторы
        return response('OK', 200);
    }
}
