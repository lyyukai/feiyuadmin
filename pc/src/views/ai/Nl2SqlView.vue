<template>
  <div class="nl2sql-view">
    <div class="view-header">
      <h2>NL2SQL <span class="fe-hide-mobile">— 自然语言转SQL</span></h2>
      <p class="header-desc fe-hide-mobile">将自然语言查询转换为SQL语句，并可直接执行</p>
    </div>

    <div class="nl2sql-workspace">
      <Nl2SqlResult />
    </div>
  </div>
</template>

<script setup>
import Nl2SqlResult from '@/components/ai/Nl2SqlResult.vue'
</script>

<style scoped>
.nl2sql-view {
  height: 100%;
  overflow-y: auto;
  background: var(--fe-bg-page);
}

.view-header {
  padding: 20px 20px 0;
}

.view-header h2 {
  margin: 0 0 8px;
  font-size: var(--fe-font-size-2xl);
  font-weight: 600;
  color: var(--fe-text-primary);
}

.header-desc {
  margin: 0;
  font-size: var(--fe-font-size-sm);
  color: var(--fe-text-secondary);
}

.nl2sql-workspace {
  padding: 16px 20px;
}

@media screen and (max-width: 767px) {
  .view-header {
    padding: 12px 12px 0;
  }

  .view-header h2 {
    font-size: var(--fe-font-size-lg);
  }

  .nl2sql-workspace {
    padding: 12px;
  }
}
</style>
