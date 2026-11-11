<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class Reportespdf extends AbstractAction
{
    public function getTitle()
    {
        return 'Reporte PDF';
    }

    public function getIcon()
    {
        return 'voyager-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.reporte-salas.detalle', [
            'id' => $this->data->id
        ]);
    }
}