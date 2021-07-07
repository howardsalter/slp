<?php
namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

final class DashboardAction
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
        $this->logger->info("Dashboard endpoint action dispatched");

        if (!isset($_COOKIE['userlogin'])) {
            // Redirect to home action
            return $response->withRedirect('/');
        }

        //Select User Info
        $sql = "SELECT gciapi.users.*, gciapi.roles.role, gciapi.roles.user_lvl, gciapi.roles.campid, slp.users.org_id, slp.organizations.name as org_name, 
                    slp.churches.id as church_id, slp.churches.name as church_name, 
                    slp.churches.city as church_city, slp.churches.state as church_state  
                FROM gciapi.users, gciapi.roles, slp.users, slp.churches, slp.organizations
                WHERE gciapi.users.userid = :userid
                AND gciapi.roles.userid = gciapi.users.userid
                AND slp.users.id = gciapi.users.portal_id
                AND slp.churches.id = slp.users.church_id
                AND slp.organizations.id = slp.users.org_id";

        //Prepare statement.
        $usr_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $usr_select->bindValue(':userid', $_COOKIE['userlogin']);

        //Execute the statement and insert our values.
        try {
            $usr_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load your information!</h1><br /><p>Error Information 54:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $usr_select->fetch();

        if ($result) {
            $_SESSION['userinfo'] = $result;
        }

        if ($_SESSION['userinfo']['role'] == 'staff') {
            return $response->withRedirect("/camp/" . $_SESSION['userinfo']['campid'] . '/manage');
        }
        //Select Camper Info

        if ($_SESSION['userinfo']['role'] == 'leader') {
            $sql = "SELECT * 
                    FROM applications
                    WHERE applications.church_id = :church_id";

            //Prepare statement.
            $app_select = $this->db->prepare($sql);

            //Bind our values to parameters.
            $app_select->bindValue(':church_id', $_SESSION['userinfo']['church_id']);

        } else {
            $sql = "SELECT * 
                    FROM applications
                    WHERE applications.church_id in ( Select slp.churches.id from slp.churches where slp.churches.org_id = :org_id )";

            //Prepare statement.
            $app_select = $this->db->prepare($sql);

            //Bind our values to parameters.
            $app_select->bindValue(':org_id', $_SESSION['userinfo']['org_id']);
        }

        //Execute the statement and insert our values.
        try {
            $app_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load camper information!</h1><br /><p>Error Information 93:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $app_select->fetchAll();

        $apps = [];
        if ($result) {
            $apps = $result;
        }

        $stats = [];
        $stats['applications'] = 0;
        $stats['volunteers'] = 0;
        foreach ($apps as $app) {
            if ($app['application_type'] == "staff"){
                $stats['volunteers']++;
            } else {
                $stats['applications']++;                
            }
        }

        if ($_SESSION['userinfo']['role'] == 'leader') {
            $sql = "SELECT SUM(spots_reserved) as camper_cnt, SUM(staff_reserved) as staff_cnt 
                FROM church_camp_reservations
                WHERE portal_church_id = :church_id
                GROUP BY portal_church_id";

            //Prepare statement.
            $user_stat = $this->db->prepare($sql);

            //Bind our values to parameters.
            $user_stat->bindValue(':church_id', $_SESSION['userinfo']['church_id']);
        } else {
            $sql = "SELECT SUM(spots_reserved) as camper_cnt, SUM(staff_reserved) as staff_cnt 
                FROM church_camp_reservations
                WHERE portal_church_id in ( Select slp.churches.id from slp.churches where slp.churches.org_id = :org_id )
                GROUP BY portal_church_id";

            //Prepare statement.
            $user_stat = $this->db->prepare($sql);

            //Bind our values to parameters.
            $user_stat->bindValue(':org_id', $_SESSION['userinfo']['org_id']);
        }    

        //Execute the statement and insert our values.
        try {
            $user_stat->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load camper information!</h1><br /><p>Error Information 143:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $user_stat->fetchAll();

        $stats['reserved'] = 0;
        if ($result) {
            if ($_SESSION['userinfo']['role'] == 'leader') {
                $stats['reserved'] = $result[0]['camper_cnt'] + $result[0]['staff_cnt'];
            } else {
                foreach ($result as $camp) {
                    $stats['reserved'] += $camp['camper_cnt'] + $camp['staff_cnt'];
                }
            }
        }

        if ($_SESSION['userinfo']['role'] == 'leader') {
            $sql = "SELECT next_session_start  
            FROM camps
            WHERE campid in (Select church_camp_reservations.camp_id from church_camp_reservations where church_camp_reservations.portal_church_id = :where_id)
            AND next_session_start > NOW()
            ORDER BY next_session_start ASC
            LIMIT 1";
            $where_id = $_SESSION['userinfo']['church_id'];
        } else {
            $sql = "SELECT next_session_start  
            FROM camps
            WHERE portal_org_id = :where_id
            AND next_session_start > NOW()
            ORDER BY next_session_start ASC
            LIMIT 1";
            $where_id = $_SESSION['userinfo']['org_id'];
        }



        //Prepare statement.
        $start_stat = $this->db->prepare($sql);

        //Bind our values to parameters.
        $start_stat->bindValue(':where_id', $where_id);

        //Execute the statement and insert our values.
        try {
            $start_stat->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load camper information!</h1><br /><p>Error Information 180:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $start_stat->fetch();

        $stats['start'] = 0;
        if ($result) {
            $date1 = new \DateTime($result['next_session_start']);
            $date2 = new \DateTime("now");
            $interval = $date1->diff($date2);

            $stats['start'] = $interval->format('%a');
        }

        if ($_SESSION['userinfo']['role'] == 'leader') {
            $sql = "SELECT *, spots_reserved, staff_reserved
            FROM camps 
                LEFT JOIN church_camp_reservations ON church_camp_reservations.camp_id = camps.campid AND church_camp_reservations.portal_church_id = :where_id
            WHERE campid in (Select camp_id from church_camp_reservations where portal_church_id = :where_id)";
            $where_id = $_SESSION['userinfo']['church_id'];
        } else {
            $sql = "SELECT *, (SELECT SUM(spots_reserved) FROM church_camp_reservations WHERE church_camp_reservations.camp_id = camps.campid GROUP BY church_camp_reservations.camp_id) as spots_reserved , 
            (SELECT SUM(staff_reserved) FROM church_camp_reservations WHERE church_camp_reservations.camp_id = camps.campid GROUP BY church_camp_reservations.camp_id) as staff_reserved 
            FROM camps 
            WHERE portal_org_id = :where_id";

            $where_id = $_SESSION['userinfo']['org_id'];
        }

        //Prepare statement.
        $camps_select = $this->db->prepare($sql);

        //Bind our values to parameters.
        $camps_select->bindValue(':where_id', $where_id);

        //Execute the statement and insert our values.
        try {
            $camps_select->execute();
        } catch(\Exception $e) {
            $error = $e->getMessage();
            return $response->write("<h1>Unable to load camper information!</h1><br /><p>Error Information 281:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
        }

        $result = $camps_select->fetchAll();

        $camps = [];
        if ($result) {
            $camps = $result;
        }

        $cntCamps = 0;
        foreach ($camps as $camp){
            if ($_SESSION['userinfo']['role'] == 'leader') {
                $sql = "SELECT applications.*, slp.churches.id as church_id, slp.churches.name as church_name, slp.churches.city as church_city, slp.churches.state as church_state
                        FROM applications, slp.churches
                        WHERE slp.churches.id = applications.church_id
                        AND camp_id = :camp_id
                        AND church_id = :where_id";
                $where_id = $_SESSION['userinfo']['church_id'];
            } else {
                $sql = "SELECT applications.*, slp.churches.id as church_id, slp.churches.name as church_name, slp.churches.city as church_city, slp.churches.state as church_state
                        FROM applications, slp.churches
                        WHERE slp.churches.id = applications.church_id
                        AND camp_id = :camp_id
                        AND church_id in ( Select slp.churches.id from slp.churches where slp.churches.org_id = :where_id )";
                $where_id = $_SESSION['userinfo']['org_id'];
            }

            //Prepare statement.
            $apps_select = $this->db->prepare($sql);

            //Bind our values to parameters.
            $apps_select->bindValue(':camp_id', $camp['campid']);
            $apps_select->bindValue(':where_id', $where_id);

            //Execute the statement and insert our values.
            try {
                $apps_select->execute();
            } catch(\Exception $e) {
                $error = $e->getMessage();
                return $response->write("<h1>Unable to load camper information!</h1><br /><p>Error Information 322:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
            }

            $result = $apps_select->fetchAll();

            $this->logger->info("Row Count: " . $apps_select->rowCount());
            $camps[$cntCamps]['apps'] = [];
            if ($apps_select->rowCount() > 0) {
                $camps[$cntCamps]['apps'] = $result;
            }

            for ($i = 0; $i < count($camps[$cntCamps]['apps']); $i++) {
                $sql = "SELECT application_data.*
                FROM application_data
                WHERE application_id = :app_id";
    
                //Prepare statement.
                $appdata_select = $this->db->prepare($sql);
    
                //Bind our values to parameters.
                $appdata_select->bindValue(':app_id', $camps[$cntCamps]['apps'][$i]['id']);
    
                //Execute the statement and insert our values.
                try {
                    $appdata_select->execute();
                } catch(\Exception $e) {
                    $error = $e->getMessage();
                    return $response->write("<h1>Unable to load camper information!</h1><br /><p>Error Information 281:<br />$error</p><button id='btnContinue' onclick='window.location.replace(\"/\")'>Return</button>");
                }
    
                $result2 = $appdata_select->fetchAll();
    
                $camps[$cntCamps]['apps'][$i]['data'] = [];
                if ($appdata_select->rowCount() > 0) {
                    $camps[$cntCamps]['apps'][$i]['data'] = $this->convertData($result2);
                }
            }    

            $cntCamps++;

        }



        $this->view->render($response, 'dashboard.twig', ["session" => $_SESSION['userinfo'], "stats" => $stats, "camps" => $camps]);

        return $response;
    }

    public function convertData($aryFields) {
        $aryReturn = [];
        for ($i = 0; $i < count($aryFields); $i++) {
            $aryReturn[$aryFields[$i]['field_name']] = $aryFields[$i]['field_value'];
        }

        return $aryReturn;
    }
}
