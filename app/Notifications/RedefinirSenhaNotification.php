<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RedefinirSenhaNotification extends Notification
{
    use Queueable;
    public $token;
    public $email;
    public $name;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $email, $name)
    {
        //
        $this->token = $token;
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {   
        $url = 'http://localhost:8000/password/reset/'.$this->token.'?email='.$this->email.$this->name;
        $minutos = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
        return (new MailMessage)
            ->subject('Atualização de senha')
            ->greeting('Olá '.$this->name.',')
            ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha da sua conta.')
            ->action('Redefinir senha', $url)
            ->line('Este link de redefinição de senha expirará em '.$minutos.' minutos.')
            ->line("Se você não solicitou uma redefinição de senha, nenhuma ação adicional será necessária.")
            ->salutation('Atenciosamente, Smed');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
