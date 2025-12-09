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
        $searchTerm = Sanitizer::sanitizeString($_GET['search'] ?? '');
        $partType = Sanitizer::sanitizeString($_GET['part_type'] ?? ''); // Retrieve part_type

        if (!empty($searchTerm)) {
            $parts = Part::search($searchTerm, $partType); // Pass partType
        } else {
            $parts = Part::findAll($partType); // Pass partType
        }

        $partTypes = \App\Models\Part::findUniquePartTypes(); // Fetch unique part types for filter display

        $this->view('public/catalog', [
            'pageTitle' => $pageTitle,
            'parts' => $parts,
            'searchTerm' => $searchTerm,
            'partTypes' => $partTypes, // Pass part types to the view
            'selectedPartType' => $partType // Pass selected part type to retain filter state
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
        $comments = Comment::findAndThreadByPartId($part->getId());
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
        $parentId = isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $commentText = Sanitizer::sanitizeString($_POST['comment_text'] ?? '');
        
        if (!isset($_SESSION['user_id'])) {
            FlashMessage::setMessages('errors', ['Necesitas iniciar sesión para poder comentar.']);
        } elseif (empty($commentText)) {
            FlashMessage::setMessages('errors', ['El comentario no puede estar vacío.']);
        } else {
            $comment = new Comment();
            $comment->setParteId($partId);
            $comment->setUsuarioId((int)$_SESSION['user_id']);
            $comment->setParentId($parentId);
            $comment->setTextoComentario($commentText);
            
            if ($comment->save()) {
                FlashMessage::setMessage('Comentario añadido con éxito.', 'success');
            } else {
                FlashMessage::setMessages('errors', ['Hubo un error al guardar tu comentario.']);
            }
        }
        
        // Redirigir de vuelta a la página de detalle
        $this->redirect('public/index.php?/part/' . $partId);
    }
}
