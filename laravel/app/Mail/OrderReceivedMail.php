<?php

namespace App\Mail; // Проверьте, что namespace совпадает с папкой

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue; // Важно для очереди!

class OrderReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function content(): Content
    {
        return new Content(
            view: 'orders.pdf.order-received', // Путь к  новому шаблону
        );
    }


}
