<?php

namespace ITColima\SiitecApi;

interface SiitecUserScopes
{
    const GET_FULL_ACCESS_USER = 'all.own:r';
    const GET_USUARIO_PERFIL_OWN= 'usuarios.perfil.own:r';
    const GET_ESCOLARES_GRUPOS_DOCENTE = 'escolares.grupos.docente:r';
    const GET_ESCOLARES_GRUPOS_ESTUDIANTE = 'escolares.grupos.estudiante:r';
}