<?php

namespace App\Mail;

use App\Models\CuentaCobro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SoporteFacturaEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CuentaCobro $cuenta
    ) {}

    public function envelope(): Envelope
    {
        $casino = $this->cuenta->casino->nombre ?? 'Casino';
        $rango = $this->cuenta->fecha_inicio->format('d/m/Y') . ' - ' . $this->cuenta->fecha_fin->format('d/m/Y');

        return new Envelope(
            subject: 'Soporte para factura - ' . $casino . ' (' . $rango . ')',
            from: config('mail.from.address'),
            replyTo: [config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.soporte-factura-enviada',
        );
    }

    /**
     * Adjuntar el PDF del soporte para factura.
     */
    public function attachments(): array
    {
        if (! $this->cuenta->archivo_pdf || ! Storage::disk('local')->exists($this->cuenta->archivo_pdf)) {
            return [];
        }

        $nombreArchivo = 'soporte-factura-' . $this->cuenta->id_cuenta . '.pdf';

        return [
            Attachment::fromPath(Storage::disk('local')->path($this->cuenta->archivo_pdf))
                ->as($nombreArchivo)
                ->withMime('application/pdf'),
        ];
    }
}
