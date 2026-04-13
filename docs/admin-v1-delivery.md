# Shopify Affiliate Admin V1（Laravel Admin 落地）

## 已落地菜单（商家后台）

1. Dashboard（`/admin`）
2. Affiliates（`/admin/affiliates`）
3. Applications（`/admin/applications`）
4. Attributions（`/admin/attributions`）
5. Commissions（`/admin/commissions`）
6. Coupons（`/admin/coupons`）
7. Payouts（`/admin/payouts`）
8. Settings（`/admin/settings`）
9. Logs
   - Webhooks（`/admin/logs/webhooks`）
   - Tracking（`/admin/logs/tracking`）

## 页面能力对齐

- **Dashboard**：总推广员、待审核申请、今日点击、今日归因订单、待审核佣金、已打款佣金、系统状态摘要。
- **Affiliates**：列表筛选 + 详情 + 编辑（基础资料、推广配置、佣金配置、收款配置）。
- **Applications**：申请列表、详情和状态编辑（pending/approved/rejected）。
- **Attributions**：归因列表与详情（只读）。
- **Commissions**：佣金列表、详情和编辑（含状态流转字段）。
- **Coupons**：优惠码列表与创建/编辑。
- **Payouts**：打款列表与创建/编辑。
- **Settings**：店铺核心配置只读视图（V1）。
- **Logs**：Webhook 与 Tracking 日志查询。

## 下一步（建议按优先级）

### P0.5（很快补齐）

- 在各资源页加 `row_action`：approve/reject/block/mark-paid/recalculate。
- Settings 由只读升级为可编辑 4 个配置 tab（basic/attribution/commission/coupon）。

### P1

- Attributions 增加 manual reassign/remove。
- Payouts 增加批次导出 CSV（对齐财务离线打款）。
- Logs 增加 errors 视图（归因异常 + 退款冲减失败）。

### P2

- 图表趋势（7 天/30 天）和多维统计报表。
- RBAC 细化（Owner/Admin/Operator/Finance/Viewer）。
- 推广员 Portal（独立登录态 + 专属链接/佣金看板）。
