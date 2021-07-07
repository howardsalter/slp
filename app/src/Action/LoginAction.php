<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class LoginAction
{
    private $view;
    private $logger;
    private $db;

    public function __construct(Twig $view, LoggerInterface $logger, PDO $db)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->db = $db;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $this->logger->info("Login action dispatched");

        if (isset($_COOKIE['userlogin'])) {
            return $response->withJson(['usrid' => $_COOKIE['userlogin']]);
        }

        $reqBody = $request->getParsedBody();

        if (isset($reqBody['email'])) {
            $email = $reqBody['email'];
        }

        if (isset($reqBody['password'])) {
            $password = $reqBody['password'];
        }

        $this->logger->info("Login Info: " . $email . " : " . $password);

        $sql = "SELECT * FROM users WHERE email = :email";

        //Prepare statement.
        $usr_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $usr_select->bindValue(':email', $email);

        //Execute the statement and insert our values.
        try {
            $usr_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            $this->logger->info("Invalid Email. Error Message: " . $error);
            return $response->withJson(['pplerror' => 'Failed to Execute.']);
        }

        $result = $usr_select->fetch();

        $hash = hash("sha512", $password . "smp2020");

        if ($hash !== $result['p_key']) {
            return $response->withJson(['pplerror' => 'Invalid Password.']);
        }

        //Password Matches

        $date_of_expiry = time() + 60 * 60 * 24 * 365;
        setcookie( "userlogin", $result['userid'], $date_of_expiry );
        return $response->withJson(['usrid' => $result['userid']]);
    }
}
