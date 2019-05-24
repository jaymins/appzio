<?php

namespace packages\actionMfood\Components;

trait HeaderImage
{
    public function getHeaderImage($title)
    {
        $object = [];
        return $this->layout->scroll[] = $this->getComponentColumn([
            $this->getComponentRow([
                $this->getHeader()
            ], [], []),
            $this->getComponentImage($this->user_data['profilepic'], [], [])
        ], [], [
            'text-align' => 'center',
        ]);
    }
}