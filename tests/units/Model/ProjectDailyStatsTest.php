<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Project;
use Kanboard\Model\ProjectDailyStats;
use Kanboard\Model\Task;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\TaskStatus;

class ProjectDailyStatsTest extends Base
{
    public function testUpdateTotals()
    {
        $p = new Project($this->container);
        $pds = new ProjectDailyStats($this->container);
        $tc = new TaskCreation($this->container);
        $ts = new TaskStatus($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));

        $this->assertEquals(1, $tc->create(array('title' => 'Task #1', 'project_id' => 1, 'date_started' => strtotime('-1 day'))));
        $this->assertEquals(2, $tc->create(array('title' => 'Task #1', 'project_id' => 1)));
        $pds->updateTotals(1, date('Y-m-d', strtotime('-1 day')));

        $this->assertTrue($ts->close(1));
        $pds->updateTotals(1, date('Y-m-d'));

        $metrics = $pds->getRawMetrics(1, date('Y-m-d', strtotime('-1days')), date('Y-m-d'));
        $expected = array(
            array(
                'day' => date('Y-m-d', strtotime('-1days')),
                'avg_lead_time' => 0,
                'avg_cycle_time' => 43200,
            ),
            array(
                'day' => date('Y-m-d'),
                'avg_lead_time' => 0,
                'avg_cycle_time' => 43200,
            )
        );

        $this->assertEquals($expected, $metrics);
    }
}
