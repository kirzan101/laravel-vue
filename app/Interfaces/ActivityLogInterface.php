<?php

namespace App\Interfaces;

interface ActivityLogInterface
{
    /**
     * Store a new activity log in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeActivityLog(array $request): array;

    /**
     * update an existing activity log in the database.
     *
     * @param array $request
     * @param integer $id
     * @return array
     */
    public function updateActivityLog(array $request, int $id): array;

    /**
     * delete a activity log from the database.
     *
     * @param integer $id
     * @return array
     */
    public function deleteActivityLog(int $id): array;
}
