<template>
  <div class="page-container">
    <el-tabs v-model="activeTab" class="notice-tabs">
      <el-tab-pane label="我的消息" name="message">
        <MessageList />
      </el-tab-pane>
      <el-tab-pane label="发送消息" name="send">
        <SendMessage />
      </el-tab-pane>
      <el-tab-pane label="渠道配置" name="channel">
        <ChannelList />
      </el-tab-pane>
      <el-tab-pane label="模板管理" name="template">
        <TemplateList />
      </el-tab-pane>
      <el-tab-pane label="发送记录" name="record">
        <RecordList />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import MessageList from './components/MessageList.vue'
import SendMessage from './components/SendMessage.vue'
import ChannelList from './channel.vue'
import TemplateList from './template.vue'
import RecordList from './record.vue'

const activeTab = ref('message')
</script>

<style scoped>
.notice-tabs {
  background: #fff;
  padding: 16px;
  border-radius: 8px;
}
</style>
