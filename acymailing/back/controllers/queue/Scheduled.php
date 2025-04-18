<?php

namespace AcyMailing\Controllers\Queue;

use AcyMailing\Classes\CampaignClass;
use AcyMailing\Classes\QueueClass;
use AcyMailing\Classes\TagClass;
use AcyMailing\Helpers\PaginationHelper;
use AcyMailing\Helpers\ToolbarHelper;
use AcyMailing\Helpers\WorkflowHelper;

trait Scheduled
{
    public function scheduled()
    {
        acym_setVar('layout', 'scheduled');

        $searchFilter = $this->getVarFiltersListing('string', 'squeue_search', '');
        $tagFilter = $this->getVarFiltersListing('string', 'squeue_tag', '');

        $pagination = new PaginationHelper();
        $campaignsPerPage = $pagination->getListLimit();
        $page = $this->getVarFiltersListing('int', 'squeue_pagination_page', 1);

        $queueClass = new QueueClass();
        $matchingElements = $queueClass->getMatchingScheduledCampaigns(
            [
                'search' => $searchFilter,
                'tag' => $tagFilter,
                'campaignsPerPage' => $campaignsPerPage,
                'offset' => ($page - 1) * $campaignsPerPage,
            ]
        );

        $pagination->setStatus($matchingElements['total'], $page, $campaignsPerPage);
        $tagClass = new TagClass();

        $viewData = [
            'allElements' => $matchingElements['elements'],
            'pagination' => $pagination,
            'search' => $searchFilter,
            'tag' => $tagFilter,
            'allTags' => $tagClass->getAllTagsByType(TagClass::TYPE_MAIL),
            'campaignClass' => new CampaignClass(),
            'languages' => acym_getLanguages(),
            'workflowHelper' => new WorkflowHelper(),
        ];

        $this->prepareScheduledToolbar($viewData);

        $this->breadcrumb[acym_translation('ACYM_SCHEDULED')] = acym_completeLink('queue&task=scheduled');
        parent::display($viewData);
    }

    public function prepareScheduledToolbar(&$data)
    {
        $toolbarHelper = new ToolbarHelper();
        $toolbarHelper->addSearchBar($data['search'], 'squeue_search');
        $toolbarHelper->addFilterByTag($data, 'squeue_tag', 'acym__queue__filter__tags acym__select');

        $data['toolbar'] = $toolbarHelper;
        if (!empty($data['tag'])) {
            $data['status_toolbar'] = [
                'squeue_tag' => $data['tag'],
            ];
        }
    }

    public function cancelScheduledSending()
    {
        $this->cancelSending();
        $this->scheduled();
    }
}
