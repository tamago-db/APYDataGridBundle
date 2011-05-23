<?php

namespace Sorien\DataGridBundle\Samples;

use Sorien\DataGridBundle\Source\Source;
use Sorien\DataGridBundle\Grid;
use Sorien\DataGridBundle\DataGrid\Row;
use Sorien\DataGridBundle\DataGrid\Rows;
use Sorien\DataGridBundle\Column\Text;
use Sorien\DataGridBundle\Column\Select;
use Sorien\DataGridBundle\Column\Range;

class Users extends Source
{
    /**
     * Prepare columns
     *
     * @param Columns $columns
     * @param $actions
     * @return null
     */
    function prepare($columns, $actions)
    {
        $columns->addColumn(new Range('v.id', 'Id', 120));

        $textColumn = new Text('v.authors', 'Authors', 200, true, true);
        $textColumn->setCallback(function($value, $row, $router) {
            return '<a style="color:#F00;" href="'.$router->generate('logout').'">'.$value.'</a>';
        });

        $columns->addColumn($textColumn)
                ->addColumn(new Select('v.mode', 'a', array('admin' => 'Admin', 'user' => 'User')))
                ->addColumn(new Text('v.admins', 'Admin', 200, true, true));

        $actions->addMassAction('v.id', function ($ids){

        });
    }

    function execute($columns, $page)
    {
        //http://www.doctrine-project.org/docs/orm/2.0/en/reference/query-builder.html
        /*
        $query = $this->get('doctrine')->getEntityManager();
        $query->select('a');

        foreach ($this->getColumns() as $column)
        {
            if ($column->isSorted())
            {
                $query->orderBy($column->getId(), $column->getOrder());
            }

            if ($column->isFiltred())
            {
                $where = $column->filtersConnected() ? $query->expr()->xand() : $query->expr()->xor();
                foreach ($column->getFilters() as $filter)
                {
                    $where->add($column->getId().' '.$filter->getFilterOperator().''.$filter->getFilterValue());
                }

                $query->addWhere($where);
            }
        }

        $query->from('Article', 'a');*/
        //$query->setMaxResults(20);

        $data = new Rows();
        for ($i = 0;$i < 20; $i++)
        {
            $row = new Row();
            foreach ($columns as $column)
            {
                $row->setField($column->getId(), $column->getTitle().'-'.$i);
                if ($i == 10)
                {
                    $row->setColor('#ffd9d5');
                }
            }

            $data->addRow($row);
        }

        $this->setTotalCount(50);

        return $data;
    }

    function onMassAction($ids, $allIds)
    {

    }
}