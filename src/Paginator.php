<?php

namespace SoftInvest\Helpers;

use InvalidArgumentException;

class Paginator
{
    public const NUM_PLACEHOLDER = '(:num)';
    protected int $totalItems;
    protected int $numPages;
    protected int $itemsPerPage;
    protected int $currentPage;
    protected string $urlPattern;
    protected int $maxPagesToShow = 10;
    protected string $previousText = 'Предыдущая';
    protected string $nextText = 'Следующая';

    /**
     * @param int $totalItems the total number of items
     * @param int $itemsPerPage the number of items per page
     * @param int $currentPage the current page number
     * @param string $urlPattern A URL for each page, with (:num) as a placeholder for the page number. Ex. '/foo/page/(:num)'
     */
    public function __construct(int $totalItems, int $itemsPerPage, int $currentPage, string $urlPattern = '')
    {
        $this->previousText = sprintf('<span class="sr-only">%s</span><svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>', __($this->previousText));
        $this->nextText = sprintf('<span class="sr-only">%s</span><svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>', __($this->nextText));
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = $currentPage;
        $this->urlPattern = $urlPattern;
        $this->updateNumPages();
    }

    protected function updateNumPages(): void
    {
        // round?
        $this->numPages = (0 == $this->itemsPerPage ? 0 : (int)ceil($this->totalItems / $this->itemsPerPage));
    }

    /**
     * @return int
     */
    public function getMaxPagesToShow(): int
    {
        return $this->maxPagesToShow;
    }

    /**
     * @param int $maxPagesToShow
     *
     * @throws \InvalidArgumentException if $maxPagesToShow is less than 3
     */
    public function setMaxPagesToShow(int $maxPagesToShow): void
    {
        if ($maxPagesToShow < 3) {
            throw new InvalidArgumentException('maxPagesToShow cannot be less than 3.');
        }
        $this->maxPagesToShow = $maxPagesToShow;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->updateNumPages();
    }

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @param int $totalItems
     */
    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
        $this->updateNumPages();
    }

    /**
     * @return int
     */
    public function getNumPages(): int
    {
        return $this->numPages;
    }

    /**
     * @return string
     */
    public function getUrlPattern(): string
    {
        return $this->urlPattern;
    }

    /**
     * @param string $urlPattern
     */
    public function setUrlPattern(string $urlPattern): void
    {
        $this->urlPattern = $urlPattern;
    }

    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * Render an HTML pagination control.
     *
     * @return string
     */
    public function toHtml(): string
    {
        if ($this->numPages <= 1) {
            return '';
        }
        $html = '<nav><ul class="inline-flex items-stretch -space-x-px">';
        if ($this->getPrevUrl()) {
            $html .= '<li><a  class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" href="' . htmlspecialchars($this->getPrevUrl()) . '">' . $this->previousText . '</a></li>';
        }
        foreach ($this->getPages() as $page) {
            if ($page['url']) {
                if ($page['isCurrent']) {
                    $html .= '<li><a href="javascript:void(0)"  class="flex items-center justify-center text-sm z-10 py-2 px-3 leading-tight text-primary-600 bg-primary-50 border border-primary-300 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">' . htmlspecialchars($page['num']) . '</a></li>';
                } else {
                    $html .= '<li><a class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" href="' . htmlspecialchars(
                            $page['url']
                        ) . '">' . htmlspecialchars($page['num']) . '</a></li>';
                }
            } else {
                $html .= '<li ><a href="javascript:void(0)" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">' . htmlspecialchars($page['num']) . '</a></li>';
            }
        }
        if ($this->getNextUrl()) {
            $html .= '<li><a class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white" href="' . htmlspecialchars($this->getNextUrl()) . '">' . $this->nextText . '</a></li>';
        }
        $html .= '</ul></nav>';

        return $html;
    }

    /**
     * @return string|null
     */
    public function getPrevUrl(): ?string
    {
        if (!$this->getPrevPage()) {
            return null;
        }

        return $this->getPageUrl($this->getPrevPage());
    }

    public function getPrevPage(): ?int
    {
        if ($this->currentPage > 1) {
            return $this->currentPage - 1;
        }

        return null;
    }

    /**
     * @param int $pageNum
     *
     * @return string
     */
    public function getPageUrl(int $pageNum): string
    {
        return str_replace(self::NUM_PLACEHOLDER, $pageNum, $this->urlPattern);
    }

    /**
     * Get an array of paginated page data.
     *
     * Example:
     * array(
     *     array ('num' => 1,     'url' => '/example/page/1',  'isCurrent' => false),
     *     array ('num' => '...', 'url' => NULL,               'isCurrent' => false),
     *     array ('num' => 3,     'url' => '/example/page/3',  'isCurrent' => false),
     *     array ('num' => 4,     'url' => '/example/page/4',  'isCurrent' => true ),
     *     array ('num' => 5,     'url' => '/example/page/5',  'isCurrent' => false),
     *     array ('num' => '...', 'url' => NULL,               'isCurrent' => false),
     *     array ('num' => 10,    'url' => '/example/page/10', 'isCurrent' => false),
     * )
     *
     * @return array
     */
    public function getPages(): array
    {
        $pages = [];
        if ($this->numPages <= 1) {
            return [];
        }
        if ($this->numPages <= $this->maxPagesToShow) {
            for ($i = 1; $i <= $this->numPages; ++$i) {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
        } else {
            // Determine the sliding range, centered around the current page.
            $numAdjacents = (int)floor(($this->maxPagesToShow - 3) / 2);
            if ($this->currentPage + $numAdjacents > $this->numPages) {
                $slidingStart = $this->numPages - $this->maxPagesToShow + 2;
            } else {
                $slidingStart = $this->currentPage - $numAdjacents;
            }
            if ($slidingStart < 2) {
                $slidingStart = 2;
            }
            $slidingEnd = $slidingStart + $this->maxPagesToShow - 3;
            if ($slidingEnd >= $this->numPages) {
                $slidingEnd = $this->numPages - 1;
            }
            // Build the list of pages.
            $pages[] = $this->createPage(1, 1 == $this->currentPage);
            if ($slidingStart > 2) {
                $pages[] = $this->createPageEllipsis();
            }
            for ($i = $slidingStart; $i <= $slidingEnd; ++$i) {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
            if ($slidingEnd < $this->numPages - 1) {
                $pages[] = $this->createPageEllipsis();
            }
            $pages[] = $this->createPage($this->numPages, $this->currentPage == $this->numPages);
        }

        return $pages;
    }

    /**
     * Create a page data structure.
     *
     * @param int $pageNum
     * @param bool $isCurrent
     *
     * @return array
     */
    protected function createPage(int $pageNum, bool $isCurrent = false): array
    {
        return [
            'num' => $pageNum,
            'url' => $this->getPageUrl($pageNum),
            'isCurrent' => $isCurrent,
        ];
    }

    /**
     * @return array
     */
    protected function createPageEllipsis(): array
    {
        return [
            'num' => '...',
            'url' => null,
            'isCurrent' => false,
        ];
    }

    public function getNextUrl(): ?string
    {
        if (!$this->getNextPage()) {
            return null;
        }

        return $this->getPageUrl($this->getNextPage());
    }

    public function getNextPage(): ?int
    {
        if ($this->currentPage < $this->numPages) {
            return $this->currentPage + 1;
        }

        return null;
    }

    public function getCurrentPageLastItem()
    {
        $first = $this->getCurrentPageFirstItem();
        if (null === $first) {
            return null;
        }
        $last = $first + $this->itemsPerPage - 1;
        if ($last > $this->totalItems) {
            return $this->totalItems;
        }

        return $last;
    }

    public function getCurrentPageFirstItem()
    {
        $first = ($this->currentPage - 1) * $this->itemsPerPage + 1;
        if ($first > $this->totalItems) {
            return null;
        }

        return $first;
    }

    public function setPreviousText($text): Paginator
    {
        $this->previousText = $text;

        return $this;
    }

    public function setNextText($text): Paginator
    {
        $this->nextText = $text;

        return $this;
    }
}
