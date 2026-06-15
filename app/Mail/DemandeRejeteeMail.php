<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeRejeteeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $demande;
    public $type;
    public $motif;
    public $logoUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $demande, $type, $motif)
    {
        $this->user = $user;
        $this->demande = $demande;
        $this->type = $type;
        $this->motif = $motif;
        $this->logoUrl = asset('assets/assets/img/logoplateau.png');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $typeLabel = match($this->type) {
            'naissance' => 'd\'extrait de naissance',
            'mariage' => 'd\'extrait de mariage',
            'deces' => 'd\'extrait de décès',
            'naissance_groupe' => 'groupée d\'extraits de naissance',
            'mariage_groupe' => 'groupée d\'extraits de mariage',
            'deces_groupe' => 'groupée d\'extraits de décès',
            default => 'de demande',
        };

        return new Envelope(
            subject: "Plateau-Apps : Votre demande {$typeLabel} a été rejetée",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.demande_rejetee',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
