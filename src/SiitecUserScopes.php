<?php

namespace ITColima\SiitecApi;

interface SiitecUserScopes
{
    public const GET_FULL_ACCESS_USER = 'all.own:r';
    public const GET_USUARIO_PERFIL_OWN = 'usuarios.perfil.own:r';
    public const GET_ESCOLARES_GRUPOS_DOCENTE = 'escolares.grupos.docente:r';
    public const GET_ESCOLARES_GRUPOS_ESTUDIANTE = 'escolares.grupos.estudiante:r';
}
