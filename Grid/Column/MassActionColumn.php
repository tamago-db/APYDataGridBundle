<?php

/*
 * This file is part of the DataGridBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 * (c) Stanislav Turza
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\DataGridBundle\Grid\Column;

class MassActionColumn extends Column
{
    const ID = '__action';

    public function __construct()
    {
        parent::__construct(array(
            'id'         => self::ID,
            'title'      => '',
            'size'       => '30px',
            'filterable' => true,
            'sortable'   => false,
            'source'     => false,
            'align'      => 'center'
        ));
    }

    public function isVisible($isExported = false)
    {
        if ($isExported) {
            return false;
        }

        return parent::isVisible();
    }

    public function getFilterType()
    {
        return $this->getType();
    }

    public function getType()
    {
        return 'massaction';
    }
}
