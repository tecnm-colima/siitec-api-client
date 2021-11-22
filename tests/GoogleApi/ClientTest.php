<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ITColima\SiitecApi\Model\Google\User;
use ITColima\SiitecApi\Resources\App\UsuariosResource;
use ITColima\SiitecApi\Resources\Google\UsersResource;
use ITColima\SiitecApi\SiitecApi;

/**
 * Conjunto de pruebas
 */
class ClientTest extends TestCase
{
    public function testCreateUser()
    {
        $siitecApi = new SiitecApi();
        $usersRes = new UsersResource($siitecApi);

        $user= new User();
        $user->email = 'pruebacliente@colima.tecnm.mx';
        $user->nombre = 'Prueba';
        $user->apellidos = 'Cliente prueba';
        $user->password = 'Qwerty123';
        $user->organization = '/Usuarios estándar/testing';

        $usersRes->create($user);

        $this->assertTrue(true);
    }

    public function testUpdateUser()
    {
        $siitecApi = new SiitecApi();
        $usersRes = new UsuariosResource($siitecApi);
    }

    public function testUpdatePhoto()
    {
        $siitecApi =  new SiitecApi();
        $usersRes = new UsuariosResource($siitecApi);
    }

    /**
     * Funcion para probar suspender
     *
     * @return void
     */
    public function testSuspendUser()
    {
        $siitecApi = new SiitecApi();
        $usersRes = new UsersResource($siitecApi);
        $usersRes->suspend('testing12345@colima.tecnm.mx');
        $this->assertTrue(true);
    }

    /**
     * Funcion para activar usuarios
     */
    public function testActivateUSer()
    {
        $siitecApi = new SiitecApi();
        $usersRes = new UsersResource($siitecApi);
        $usersRes->activate('testing12345@colima.tecnm.mx');
        $this->assertTrue(true);
    }
}