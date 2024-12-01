<?php
require_once 'models/Contact.php';
require_once 'config/database.php';

class ContactController {
    private $contact;
    private $db;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->contact = new Contact($db);
    }

    private function render($view, $data = []) {
        extract($data);
        ob_start();
        include __DIR__ . "/../views/contacts/{$view}.php";
        $content = ob_get_clean();
        include __DIR__ . "/../views/layout.php";
    }

    public function index() {
        $this->render('index');
    }

    public function list() {
        try {
            $result = $this->contact->read();
            $contacts = $result->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode([
                'data' => $contacts
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function getContact($id) {
        try {
            $this->contact->id = $id;
            $result = $this->contact->readOne();
            $contact = $result->fetch(PDO::FETCH_ASSOC);
            
            if ($contact) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $contact
                ]);
            } else {
                throw new Exception('Contacto no encontrado');
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->contact->name = $_POST['name'];
                $this->contact->phone = $_POST['phone'];
                $this->contact->email = $_POST['email'];
                $this->contact->address = $_POST['address'];

                if ($this->contact->create()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Contacto creado exitosamente'
                    ]);
                } else {
                    throw new Exception('Error al crear el contacto');
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        }
        
        $this->render('create');
    }

    public function update($id) {
        try {
            $this->contact->id = $id;
            
            // Obtener los datos del POST
            // Si no es JSON, usar POST normal
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                $data = $_POST; 
            }
            
            $this->contact->name = $data['name'];
            $this->contact->phone = $data['phone'];
            $this->contact->email = $data['email'];
            $this->contact->address = $data['address'];

            if ($this->contact->update()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Contacto actualizado exitosamente'
                ]);
            } else {
                throw new Exception('Error al actualizar el contacto');
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function delete($id) {
        try {
            $this->contact->id = $id;
            if ($this->contact->delete()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Contacto eliminado exitosamente'
                ]);
            } else {
                throw new Exception('Error al eliminar el contacto');
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }


}