<?php

namespace Appsas\Controllers;

use Appsas\App;
use Appsas\Database;
use Appsas\FS;
use Appsas\Request;
use Appsas\Response;
use Appsas\Validator;

class PersonController extends BaseController
{
    public const TITLE = 'Asmenys';

    public function list(Request $request): Response
    {
        $db = App::resolve(Database::class);

        $kiekis = $request->get('amount', 10);
        $orderBy = $request->get('orderby', 'id');

        $asmenys = $db->query('SELECT p.*, concat(c.title, \' - \', a.city, \' - \', a.street, \' - \', a.postcode) address
FROM persons p
    LEFT JOIN addresses a on p.address_id = a.id 
    LEFT JOIN countries c on a.country_iso = c.iso 
ORDER BY ' . $orderBy . ' DESC LIMIT ' . $kiekis);

        $rez = $this->generatePersonsTable($asmenys);

        return $this->render('person/list', $rez);
    }

    public function new(): Response
    {
        return $this->render('person/new');
    }

    public function store(Request $request): Response
    {
        Validator::required($request->get('first_name'));
        Validator::required($request->get('last_name'));
        Validator::required((int)$request->get('code'));
        Validator::numeric((int)$request->get('code'));
        Validator::asmensKodas((int)$request->get('code'));

        $db = App::resolve(Database::class);

        $db->query(
            "INSERT INTO `persons` (`first_name`, `last_name`, `code`)
                    VALUES (:first_name, :last_name, :code)",
            $request->all()
        );

        return $this->redirect('/persons', ['message' => "Record created successfully"]);
    }

    public function delete(Request $request): Response
    {
        $kuris = (int)$request->get('id');

        Validator::required($kuris);
        Validator::numeric($kuris);
        Validator::min($kuris, 1);

        $db = App::resolve(Database::class);

        $db->query("DELETE FROM `persons` WHERE `id` = :id", ['id' => $kuris]);

        return $this->redirect('/persons', ['message' => "Record deleted successfully"]);
    }

    public function edit(Request $request): Response
    {
        $db = App::resolve(Database::class);

        $person = $db->query("SELECT * FROM `persons` WHERE `id` = :id", ['id' => $request->get('id')])[0];

        return $this->render('person/edit', $person);
    }

    public function update(Request $request): Response
    {
        Validator::required($request->get('first_name'));
        Validator::required($request->get('last_name'));
        Validator::required($request->get('code'));
        Validator::numeric($request->get('code'));
        Validator::asmensKodas($request->get('code'));

        $db = App::resolve(Database::class);

        $db->query(
            "UPDATE `persons` 
                    SET `first_name` = :first_name, 
                        `last_name` = :last_name, 
                        `code` = :code, 
                        `email` = :email,          
                        `phone` = :phone, 
                        `address_id` = :address_id 
                    WHERE `id` = :id",
            $request->all()
        );

        return $this->redirect('/person/show?id='.$request->get('id'), ['message' => "Record updated successfully"]);
    }

    public function show(Request $request): Response
    {
        $db = App::resolve(Database::class);

        $person = $db->query("SELECT * FROM `persons` WHERE `id` = :id", ['id' => $request->get('id')])[0];

        return $this->render('person/show', $person);
    }

    /**
     * @param mixed $person
     * @return string
     */
    protected function generatePersonRow(array $person): string
    {
        /** @var FS $fs */
        $fs = App::resolve(FS::class);
        $fs->setFile('../src/html/person/person_row.html');
        $fileContent = $fs->getFileContent();
        foreach ($person as $key => $item) {
            $fileContent = str_replace("{{" . $key . "}}", $item ?? 'NULL', $fileContent);
        }

        return $fileContent;
    }

    /**
     * @param array $asmenys
     * @return string
     */
    protected function generatePersonsTable(array $asmenys): string
    {
        $rez = '<table class="highlight striped">
            <tr>
                <th>ID</th>
                <th>Vardas</th>
                <th>Pavarde</th>
                <th>Emailas</th>
                <th>Asmens kodas</th>
                <th><a href="/persons?orderby=phone">TEl</a></th>
                <th>Addr.ID</th>
                <th>Veiksmai</th>
            </tr>';
        foreach ($asmenys as $asmuo) {
            $rez .= $this->generatePersonRow($asmuo);
        }
        $rez .= '</table>';
        return $rez;
    }
}