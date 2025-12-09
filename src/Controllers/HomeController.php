<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Part;
use App\Models\Comment;
use App\Helpers\Sanitizer;
use App\Helpers\FlashMessage;

class HomeController extends BaseController
{
    /**
     * Muestra el catálogo público de partes.
     */
    public function index(): void
    {
        $pageTitle = 'Catálogo de Partes';
        $parts = Part::findAll(); // Asumiendo que este método ya es adecuado

        // No es necesario un layout completo, la vista ya lo contiene
        $this->view('public/catalog', [
            'pageTitle' => $pageTitle,
            'parts' => $parts
        ]);
    }

    /**
     * Muestra la página de detalle de una parte específica.
     */
    public function show(): void
    {
        $partId = (int)($this->params['id'] ?? 0);
        $part = Part::findById($partId);

        if (!$part) {
            // Si no se encuentra la parte, redirigir al catálogo
            $this->redirect('public/index.php');
            return;
        }

        $pageTitle = htmlspecialchars($part->getNombre());
        $comments = Comment::findAllByPartId($part->getId(), 'aprobado');
        $errors = FlashMessage::getMessages('errors'); // Recuperar errores si los hay
        $successMessage = FlashMessage::getMessages('success'); // Recuperar mensajes de éxito

        $this->view('public/detalle', [
            'pageTitle' => $pageTitle,
            'part' => $part,
            'comments' => $comments,
            'errors' => $errors,
            'successMessage' => $successMessage ? $successMessage[0] : null
        ]);
    }
    
    /**
     * Procesa el envío de un nuevo comentario.
     */
    public function addComment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('public/index.php');
            return;
        }

        $partId = (int)($_POST['part_id'] ?? 0);
        $commentText = Sanitizer::sanitizeString($_POST['comment_text'] ?? '');
        
        if (!isset($_SESSION['user_id'])) {
            FlashMessage::setMessages('errors', ['Necesitas iniciar sesión para poder comentar.']);
        } elseif (empty($commentText)) {
            FlashMessage::setMessages('errors', ['El comentario no puede estar vacío.']);
        } else {
            $comment = new Comment();
            $comment->setParteId($partId);
            $comment->setUsuarioId((int)$_SESSION['user_id']);
            $comment->setTextoComentario($commentText);
            
            if ($comment->save()) {
                FlashMessage::setMessages('success', ['Tu comentario ha sido enviado y está pendiente de aprobación.']);
            } else {
                FlashMessage::setMessages('errors', ['Hubo un error al guardar tu comentario.']);
            }
        }
        
        // Redirigir de vuelta a la página de detalle
        $this->redirect('public/index.php?/part/' . $partId);
    }
}
