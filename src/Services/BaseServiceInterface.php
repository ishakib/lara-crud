<?php


namespace App\Services;
interface BaseServiceInterface
{
    public function all();

    public function show($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}
