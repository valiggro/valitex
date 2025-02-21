<?php

namespace App\Model\AnafEfactura;

use Anaf\Responses\Efactura\Message;

class MessageModel
{
    private Message $message;

    public function with(Message $message): static
    {
        $new = clone $this;
        $new->message = $message;
        return $new;
    }

    public function getSupplierId(): int
    {
        switch ($this->message->type) {
            case 'FACTURA PRIMITA':
                if (preg_match(
                    pattern: '/Factura cu id_incarcare=\d+ emisa de cif_emitent=(\d+) pentru cif_beneficiar=\d+/',
                    subject: $this->message->details,
                    matches: $matches
                )) {
                    return $matches[1];
                }
                return -1;
            case 'FACTURA TRIMISA':
                if (preg_match(
                    pattern: '/Factura cu id_incarcare=\d+ transmisa de cif=\d+  ca autofactutra in numele cif=(\d+)/',
                    subject: $this->message->details,
                    matches: $matches
                )) {
                    return $matches[1];
                }
                return -1;
            default:
                return -1;
        }
    }
}
