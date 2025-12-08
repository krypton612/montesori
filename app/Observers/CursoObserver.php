<?php

namespace App\Observers;

use App\Models\Curso;

class CursoObserver
{
    /**
     * Handle the Curso "created" event.
     */

    public function created(Curso $curso): void
    {

    }

    /**
     * Handle the Curso "updated" event.
     */
    public function updated(Curso $curso): void
    {
        //
    }

    /**
     * Handle the Curso "deleted" event.
     */
    public function deleted(Curso $curso): void
    {
        //
    }

    /**
     * Handle the Curso "restored" event.
     */
    public function restored(Curso $curso): void
    {
        //
    }

    /**
     * Handle the Curso "force deleted" event.
     */
    public function forceDeleted(Curso $curso): void
    {
        //
    }


    public function retrieved(Curso $curso): void
    {

    }
}
