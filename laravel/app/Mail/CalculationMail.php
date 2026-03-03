<?php

    namespace App\Mail;


    use App\Models\Order;
    use Barryvdh\DomPDF\Facade\Pdf;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Mail\Mailable;
    use Illuminate\Mail\Mailables\Content;
    use Illuminate\Mail\Mailables\Envelope;
    use Illuminate\Queue\SerializesModels;

    class CalculationMail extends Mailable implements ShouldQueue
    {
        use Queueable, SerializesModels;

        public $order;
        public $priceGroup;

        public function __construct(Order $order, string $priceGroup = 'retail')
        {
            // Используем SerializesModels, он сам подгрузит свежий объект из базы по ID
            $this->order = $order;
            $this->priceGroup = $priceGroup;
        }

        public function build()
        {
            // 1. Увеличиваем лимит памяти для DomPDF
            ini_set('memory_limit', '512M');

            // 2. Убеждаемся, что все связи подгружены (в очереди они могут пропасть)
            $this->order->load(['customer', 'colorCatalog', 'colorCode', 'coatingType', 'milling', 'items.drilling', 'items.facadeType', 'items.thickness']);

            // 3. Генерируем PDF
            $pdf = Pdf::loadView('orders.pdf.calculation-client', [
                'order' => $this->order,
                'priceGroup' => $this->priceGroup,
            ]);

            return $this->from(config('mail.from.address'), config('mail.from.name'))
                ->subject("Расчёт заказа №{$this->order->queue_number}")
                ->view('emails.calculation-text')
                ->attachData(
                    $pdf->output(),
                    "order-{$this->order->queue_number}-calculation.pdf",
                    ['mime' => 'application/pdf']
                );
        }
        /**
         * Get the message envelope.
         */
        /*public function envelope(): Envelope
        {
            return new Envelope(
                subject: 'Calculation Mail',
            );
        }

        /**
         * Get the message content definition.
         */
       /* public function content(): Content
        {
            return new Content(
                view: 'view.name',
            );
        }

        /**
         * Get the attachments for the message.
         *
         * @return array<int, \Illuminate\Mail\Mailables\Attachment>
         */
       /* public function attachments(): array
        {
            return [];
        }*/
    }
