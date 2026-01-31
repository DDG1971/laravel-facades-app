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
        /**
         * Create a new message instance.
         */
        public function __construct(Order $order, string $priceGroup = 'retail')
        {
            $this->order = $order;
            $this->priceGroup = $priceGroup;
        }
        public function build()
        {
            $pdf = Pdf::loadView('orders.pdf.calculation-client', [
                'order' => $this->order,
                'priceGroup' => $this->priceGroup,

                ]);

            return $this->subject("Расчёт заказа №{$this->order->id}")
                ->view('emails.calculation') // простой текст письма
                ->attachData($pdf->output(), "order-{$this->order->id}-calculation.pdf");
        }
        /**
         * Get the message envelope.
         */
        public function envelope(): Envelope
        {
            return new Envelope(
                subject: 'Calculation Mail',
            );
        }

        /**
         * Get the message content definition.
         */
        public function content(): Content
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
        public function attachments(): array
        {
            return [];
        }
    }
