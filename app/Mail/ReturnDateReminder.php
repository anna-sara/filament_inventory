<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reserveditem;
use App\Models\Item;
use Carbon\Carbon;

class ReturnDateReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Reserveditem $reservation )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Påminnelse om återlämning',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $date = new Carbon($this->reservation->return_date);
        return new Content(
            view: 'emails.user.returndatereminder',
            with: [
                'reservationName' => $this->reservation->username,
                'reservationDesc' => Item::where('id', $this->reservation->item_id)->pluck('desc')->first(),
                'reservationEmail' => $this->reservation->email,
                'reservationReturnDate' => $date->format('Y-m-d'),
            ],
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
