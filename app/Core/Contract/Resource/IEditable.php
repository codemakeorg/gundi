<?php
namespace Core\Contract\Resource;

interface IEditable
{
    /**
     * Show edit form
     * @param string|int $mId
     */
    public function edit($mId);

    /**
     * Update
     * @param string|int $mId
     */
    public function update($mId);
}