<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Comment;
use App\Helpers\FlashMessage;

class CommentController extends BaseController
{
    /**
     * Muestra la lista de comentarios para moderación.
     */
    public function index(): void
    {
        $this->authorizeAdmin();

        $pageTitle = 'Moderación de Comentarios';
        $comments = Comment::findAllForAdmin(); // Este método ya junta usuario y parte.

        $this->view('admin/comments/index', [
            'pageTitle' => $pageTitle,
            'comments' => $comments
        ]);
    }

    /**
     * Procesa la aprobación, rechazo o eliminación de un comentario.
     */
    public function action(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Solo procesamos POST para acciones
            $this->redirect('public/index.php?/admin/comentarios');
            return;
        }

        $actionType = $_POST['action'] ?? null;
        $commentId = isset($_POST['id']) ? (int)$_POST['id'] : null;

        if (!$actionType || !$commentId) {
            FlashMessage::setMessage('Acción o ID de comentario inválido.', 'danger');
            $this->redirect('public/index.php?/admin/comentarios');
            return;
        }

        $comment = Comment::findById($commentId);

        if (!$comment) {
            FlashMessage::setMessage('Comentario no encontrado.', 'danger');
            $this->redirect('public/index.php?/admin/comentarios');
            return;
        }

        switch ($actionType) {
            case 'approve':
                if ($comment->updateStatus('aprobado')) {
                    FlashMessage::setMessage('Comentario aprobado.', 'success');
                } else {
                    FlashMessage::setMessage('Error al aprobar el comentario.', 'danger');
                }
                break;
            case 'reject':
                if ($comment->updateStatus('rechazado')) {
                    FlashMessage::setMessage('Comentario rechazado.', 'warning');
                } else {
                    FlashMessage::setMessage('Error al rechazar el comentario.', 'danger');
                }
                break;
            case 'delete':
                if ($comment->delete()) {
                    FlashMessage::setMessage('Comentario eliminado permanentemente.', 'warning');
                } else {
                    FlashMessage::setMessage('Error al eliminar el comentario.', 'danger');
                }
                break;
            default:
                FlashMessage::setMessage('Acción no reconocida.', 'danger');
                break;
        }
        $this->redirect('public/index.php?/admin/comentarios');
    }
}
