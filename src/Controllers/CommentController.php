<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Comment;
use App\Helpers\FlashMessage;

class CommentController extends BaseController
{
    /**
     * Deletes a comment. Admin only.
     */
    public function delete(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to home if accessed directly
            $this->redirect('public/index.php');
            return;
        }

        $commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
        $partId = isset($_POST['part_id']) ? (int)$_POST['part_id'] : 0; // For redirecting back

        if (!$commentId || !$partId) {
            FlashMessage::setMessage('Solicitud inválida.', 'danger');
            $this->redirect('public/index.php');
            return;
        }

        $comment = Comment::findById($commentId);

        if ($comment) {
            if ($comment->delete()) {
                FlashMessage::setMessage('Comentario eliminado con éxito.', 'success');
            } else {
                FlashMessage::setMessage('No se pudo eliminar el comentario.', 'danger');
            }
        } else {
            FlashMessage::setMessage('El comentario no existe o ya fue eliminado.', 'warning');
        }

        // Redirect back to the part detail page
        $this->redirect('public/index.php?/part/' . $partId);
    }
}
