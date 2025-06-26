<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UserEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    public $details;
    protected $storage_disk;

    /**
     * Create a new message instance.
     *
     * @param array $details
     */
    public function __construct(User $user, $details)
    {
        $this->user = $user;
        $this->details = $details;
        $this->storage_disk = env('STORAGE_DISK', 'public');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from(
            $this->details['from'] ?? env('MAIL_FROM_ADDRESS', 'etap-vending-support@etapinc.com'),
            $this->details['MAIL_NAME'] ?? 'etap-vending-no-reply'
        )
            ->to($this->user->email) // Moved before `view()`
            ->cc($this->details['cc'] ?? [])
            ->subject($this->details['subject'])
            ->view($this->details['template'] ?? '')
            ->with(['data' => $this->details, 'to' => $this->user]);

        // Check if an attachment path is provided
        if (!empty($this->details['attachment'])) {
            $filePath = $this->details['attachment'];
            if (Storage::disk($this->storage_disk)->exists($filePath)) {
                $fileStream = Storage::disk($this->storage_disk)->readStream($filePath);
                if ($fileStream) {
                    $email->attachData(
                        stream_get_contents($fileStream), // Convert stream to string
                        basename($filePath) // Use the actual filename
                    );
                    fclose($fileStream); // Close the stream after reading
                }
            }
        }

        return $email; // Ensure to return the email object
    }
}
