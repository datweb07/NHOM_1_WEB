<?php
// Breadcrumbs should be passed from the page view
$breadcrumbs = $breadcrumbs ?? [['label' => 'Dashboard', 'url' => '']];
$lastBreadcrumb = end($breadcrumbs);
$pageTitle = $lastBreadcrumb['label'] ?? 'Dashboard';
?>
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Row-->
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0"><?= htmlspecialchars($pageTitle) ?></h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
              <?php if ($index === count($breadcrumbs) - 1): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($crumb['label'] ?? '') ?></li>
              <?php else: ?>
                <li class="breadcrumb-item">
                  <a href="<?= htmlspecialchars($crumb['url'] ?? '#') ?>"><?= htmlspecialchars($crumb['label'] ?? '') ?></a>
                </li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ol>
        </div>
      </div>
      <!--end::Row-->
    </div>
    <!--end::Container-->
  </div>