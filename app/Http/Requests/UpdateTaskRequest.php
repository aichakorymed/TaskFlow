<?php

namespace App\Http\Requests;

/**
 * Les règles de modification sont identiques à celles de la création :
 * on hérite de StoreTaskRequest pour ne pas dupliquer le code.
 * (Si les règles divergent un jour, il suffira de surcharger rules() ici.)
 */
class UpdateTaskRequest extends StoreTaskRequest
{
}
