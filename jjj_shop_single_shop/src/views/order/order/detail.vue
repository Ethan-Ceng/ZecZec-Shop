<template>

	<div class="pb100" style="padding-bottom: 100px;" v-loading="loading">
		<!--内容-->
		<div class="product-content">
			<!--基本信息-->
			<div class="common-form">基本信息</div>
			<div class="table-wrap">
				<el-row>
					<el-col :span="5">
						<div class="pb16">
							<span class="gray9">订单号：</span>
							{{ detail.order_no }}
						</div>
					</el-col>
					<el-col :span="5">
						<div class="pb16">
							<span class="gray9">买家：</span>
							{{ detail.user.nickName }}
							<span>用户ID：({{ detail.user.user_id }})</span>
						</div>
					</el-col>
					<el-col :span="5">
						<div class="pb16">
							<span class="gray9">订单金额 (元)：</span>
							{{ detail.order_price }}
						</div>
					</el-col>
					<el-col :span="5">
						<div class="pb16">
							<span class="gray9">运费金额 (元)：</span>
							{{ detail.express_price }}
						</div>
					</el-col>
					<el-col :span="5" v-if="detail.coupon_money > 0">
						<div class="pb16">
							<span class="gray9">优惠券抵扣 (元)：</span>
							{{ detail.coupon_money }}
						</div>
					</el-col>
					<el-col :span="5" v-if="detail.points_money > 0">
						<div class="pb16">
							<span class="gray9">积分抵扣 (元)：</span>
							{{ detail.points_money }}
						</div>
					</el-col>
					<el-col :span="5" v-if="detail.fullreduce_money > 0">
						<div class="pb16">
							<span class="gray9">满减金额 (元)：</span>
							{{ detail.fullreduce_money }}
						</div>
					</el-col>
					<el-col :span="5" v-if="detail.order_source==80">
						<div class="pb16">
							<span class="gray9">定金：</span>
							{{ detail.advance.pay_price }}
						</div>
					</el-col>
					<el-col :span="5" v-if="detail.order_source==80">
						<div class="pb16">
							<span class="gray9">尾款：</span>
							{{ detail.total_price }}
						</div>
					</el-col>
					<el-col :span="5" v-if="detail.order_source==80&&detail.advance.reduce_money">
						<div class="pb16">
							<span class="gray9">尾款立减：</span>
							{{ detail.advance.reduce_money }}
						</div>
					</el-col>
					<el-col :span="5">
						<div class="pb16">
							<span class="gray9">实付款金额 (元)：</span>
							{{ detail.pay_price }}
						</div>
					</el-col>
					<el-col :span="5">
						<div v-if="detail.pay_status.value==20">
							<div class="pb16" v-if="detail.pay_type.value!=10&&detail.balance>0">
								<span class="gray9">支付方式：</span>
								{{ detail.pay_type.text }}({{ detail.online_money }})+ 余额支付({{ detail.balance }})
							</div>
							<div v-else><span class="gray9">支付方式：</span>{{ detail.pay_type.text }}</div>
						</div>
						<div v-else><span class="gray9">支付方式：</span>{{ detail.pay_type.text }}</div>
					</el-col>
					<el-col :span="5">
						<div class="pb16">
							<span class="gray9">配送方式：</span>
							{{ detail.delivery_type.text }}
						</div>
					</el-col>
					<el-col :span="5">
						<div class="pb16">
							<span class="gray9">下单时间：</span>
							{{ detail.create_time }}
						</div>
					</el-col>
					<el-col :span="5" v-if="detail.receipt_time">
						<div class="pb16">
							<span class="gray9">完成时间：</span>
							{{ detail.receipt_time }}
						</div>
					</el-col>
					<el-col :span="5">
						<div class="pb16">
							<span class="gray9">交易状态：</span>
							{{ detail.order_status.text }}
						</div>
					</el-col>
					<el-col :span="5"
						v-if="detail['pay_status']['value'] == 10 && detail['order_status']['value'] == 10 && detail['order_source'] == 10"
						v-auth="'/order/order/updatePrice'">
						<el-button @click="editClick(detail)" size="small">修改价格</el-button>
					</el-col>
				</el-row>
			</div>
			<!--编辑-->
			<Add v-if="open_edit" :open_edit="open_edit" :order="userModel" @close="closeDialogFunc($event, 'edit')">
			</Add>
			<!--商品信息-->
			<div class="common-form mt16">商品信息</div>
			<div class="table-wrap">
				<el-table size="small" :data="detail.product" border style="width: 100%">
					<el-table-column prop="product_name" label="商品" width="400">
						<template #default="scope">
							<div class="product-info">
								<div class="pic"><img v-img-url="scope.row.image.file_path" /></div>
								<div class="info">
									<div class="name">
										{{ scope.row.product_name }}
										<span v-if="scope.row.is_gift==1" class="red">赠品</span>
									</div>
									<div class="gray9" v-if="scope.row.product_attr!=''">{{scope.row.product_attr}}
									</div>
									<div class="orange" v-if="scope.row.refund">
										{{ scope.row.refund.type.text }}-{{ scope.row.refund.status.text }}
									</div>
									<div class="price">
										<span
											:class="{'text-d-line':scope.row.is_user_grade==1,'gray6':scope.row.is_user_grade!=1}">￥
											{{ scope.row.product_price }}</span>
										<span class="ml10" v-if="scope.row.is_user_grade==1">
											会员折扣价：￥ {{ scope.row.grade_product_price }}
										</span>
									</div>
								</div>
							</div>
						</template>
					</el-table-column>
					<el-table-column prop="product_no" label="商品编码"></el-table-column>
					<el-table-column prop="product_weight" label="重量 (Kg)"></el-table-column>
					<el-table-column prop="total_num" label="购买数量">
						<template #default="scope">
							<p>x {{ scope.row.total_num }}</p>
						</template>
					</el-table-column>
					<el-table-column prop="total_price" label="商品总价(元)">
						<template #default="scope">
							<p>￥ {{ scope.row.total_price }}</p>
						</template>
					</el-table-column>
					<!--表单信息-->
					<el-table-column label="表单信息">
						<template #default="scope">
							<div v-if="scope.row.table_record_id > 0">
								<div v-for="(item, index) in scope.row.tableData" :key="index">
									{{item.name}}:{{item.value}}
								</div>
							</div>
						</template>
					</el-table-column>
				</el-table>
			</div>
			<!--收货信息-->
			<div v-if="detail.delivery_type.value == 10">
				<div class="common-form mt16">收货信息</div>
				<div class="table-wrap">
					<el-row>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">收货人：</span>
								{{ detail.address.name }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">收货电话：</span>
								{{ detail.address.phone }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">收货地址：</span>
								{{ detail.address.region.province }} {{ detail.address.region.city }}
								{{ detail.address.region.region }}
								{{ detail.address.detail }}
							</div>
						</el-col>
						<el-col :span="5" v-if="detail.delivery_status.value!=20 && detail.order_status.value==10">
							<div class="pb16">
								<el-button type="primary" link size="small" @click='changeAdd'> 修改地址</el-button>
							</div>
						</el-col>
					</el-row>
					<el-row>
						<el-col :span="25">
							<div class="pb16">
								<span class="gray9">备注：</span>
								{{ detail.buyer_remark }}
							</div>
						</el-col>
					</el-row>
				</div>
			</div>
			<!--自提门店信息-->
			<template v-if="detail.delivery_type.value == 20">
				<div class="common-form mt16">自提用户信息</div>
				<div class="table-wrap" v-if="detail.extract">
					<el-row>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">联系人：</span>
								{{ detail.extract.linkman }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">联系电话：</span>
								{{ detail.extract.phone }}
							</div>
						</el-col>
					</el-row>
					<el-row>
						<el-col :span="25">
							<div class="pb16">
								<span class="gray9">备注：</span>
								{{ detail.buyer_remark }}
							</div>
						</el-col>
					</el-row>
				</div>
				<div class="common-form mt16">自提信息</div>
				<div class="table-wrap" v-if="detail.extract">
					<el-row>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">门店ID：</span>
								{{ detail.extractStore.store_id }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">门店名称：</span>
								{{ detail.extractStore.store_name }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">联系人：</span>
								{{ detail.extractStore.linkman }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">联系电话：</span>
								{{ detail.extractStore.phone }}
							</div>
						</el-col>
						<el-col :span="15">
							<div class="pb16">
								<span class="gray9">门店地址：</span>
								{{ detail.extractStore.region.province }}- {{ detail.extractStore.region.city }}-
								{{ detail.extractStore.region.region }}-
								{{ detail.extractStore.address }}
							</div>
						</el-col>
					</el-row>
				</div>
			</template>
			<!--无需发货-->
			<template v-if="detail.delivery_type.value == 30">
				<div class="common-form mt16">用户信息</div>
				<div class="table-wrap">
					<el-row>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">联系手机：</span>
								{{ detail.user.mobile }}
							</div>
						</el-col>
					</el-row>
					<el-row>
						<el-col :span="25">
							<div class="pb16">
								<span class="gray9">备注：</span>
								{{ detail.buyer_remark }}
							</div>
						</el-col>
					</el-row>
				</div>
			</template>
			<!--付款信息-->
			<div v-if="detail.pay_status.value == 20">
				<div class="common-form mt16">付款信息</div>
				<div class="table-wrap">
					<el-row>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">应付款金额：</span>
								{{ detail.pay_price }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">支付方式：</span>
								{{ detail.pay_type.text }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">支付流水号：</span>
								{{ detail.transaction_id }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">付款状态：</span>
								{{ detail.pay_status.text }}
							</div>
						</el-col>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">付款时间：</span>
								{{ detail.pay_time }}
							</div>
						</el-col>
					</el-row>
				</div>
			</div>
			<!--  用户取消订单 -->
			<div v-if="detail.pay_status.value == 20 && detail.order_status.value == 21"
				v-auth="'/order/operate/confirmCancel'">
				<div class="common-form mt16">用户取消订单</div>
				<p class="red pb16">当前买家已付款并申请取消订单，请审核是否同意，如同意则自动退回付款金额（微信支付原路退款）并关闭订单。</p>
				<el-form size="small" ref="forms" :model="forms">
					<el-form-item label="审核状态">
						<div>
							<el-radio v-model="forms.is_cancel" :label="1">同意</el-radio>
							<el-radio v-model="forms.is_cancel" :label="0">拒绝</el-radio>
						</div>
					</el-form-item>
				</el-form>
			</div>
			<!--发货信息-->
			<div v-if="detail.pay_status.value == 20 && detail.delivery_type.value == 10 && [20, 21].indexOf(detail.order_status.value) === -1 && (!detail.assemble_status || detail.assemble_status == 20)"
				v-auth="'/order/order/delivery'">
				<!-- <div v-if="detail.delivery_status.value == 10"> -->
				<!-- 发货的数据 -->
				<div v-if="detail.delivery_status.value == 20 || detail.delivery_status.value == 30">
					<!-- 单包裹发货信息 -->
					<div class="common-form mt16">发货信息</div>
					<div class="table-wrap">
						<div v-if="detail.delivery_type.value == 10 && detail.is_single==0">
							<el-row>
								<el-col :span="5">
									<div class="pb16">
										<span class="gray9">物流公司：</span>
										{{ detail.express.express_name }}
									</div>
								</el-col>
								<el-col :span="5">
									<div class="pb16">
										<span class="gray9">物流单号：</span>
										{{ detail.express_no }}
									</div>
								</el-col>
								<el-col :span="5">
									<div class="pb16">
										<span class="gray9">发货状态：</span>
										{{ detail.delivery_status.text }}
									</div>
								</el-col>
								<el-col :span="5">
									<div class="pb16">
										<span class="gray9">发货时间：</span>
										{{ detail.delivery_time }}
									</div>
								</el-col>
								<el-col :span="5" v-if="detail.is_label">
									<div class="pb16 d-s-s">
										<div class="gray9">电子面单：</div>
										<el-image style="width: 100px;" :src="detail.label" :zoom-rate="1.2"
											:max-scale="7" :min-scale="0.2" :preview-src-list="[detail.label]"
											:initial-index="0" fit="cover" />
										<!-- <img v-img-url="detail.label" :width="100" /> -->
									</div>
								</el-col>

								<el-col :span="5" v-if="detail.pay_status.value == 20 && detail.delivery_status.value != 10 && detail.order_status.value == 10
								    && detail.label_print_type == 1
								&& detail.delivery_type.value == 10 && detail.is_label == 1 && detail.receipt_status.value == 10"
									v-auth="'/order/order/printRepeate'">
									<el-button @click="printOld(detail)" type="text" size="small">电子面单复打
									</el-button>
								</el-col>

								<el-col :span="5" v-if="detail.pay_status.value == 20 && detail.delivery_status.value != 10 && detail.order_status.value == 10
								&& detail.delivery_type.value == 10 && detail.is_label == 1 && detail.receipt_status.value == 10"
									v-auth="'/order/order/labelCancel'">
									<el-button @click="labelCancel(detail)" type="text" size="small">电子面单取消
									</el-button>
								</el-col>

							</el-row>
						</div>
						<!-- 多包裹发货信息 -->
						<div v-else-if="detail.delivery_type.value == 10 && detail.is_single==1">
							<el-table size="small" :data="detail.orderDeliverList" border style="width: 100%">
								<el-table-column prop="product_name" label="#" width="100">
									<template #default="{ $index }">
										包裹{{ $index + 1 }}
									</template>
								</el-table-column>
								<el-table-column prop="product_name" label="商品">
									<template #default="{ row }">
										<template v-for="v in row.product_list" :key="v">
											<div class="product-info pb16">
												<div class="pic"><img v-img-url="v.image_path" /></div>
												<div class="info d-b-s d-c">
													<div class="name">{{ v.product_name }}</div>
													<div class="price">发货数：{{ v.delivery_num }}</div>
												</div>
											</div>
										</template>
									</template>
								</el-table-column>
								<el-table-column label="电子面单">
									<template #default="{ row }">
										<el-image v-if="row.label" style="width: 100px; height: 100px" :src="row.label"
											:zoom-rate="1.2" :max-scale="7" :min-scale="0.2"
											:preview-src-list="[row.label]" :initial-index="0" fit="cover" />
									</template>
								</el-table-column>
								<el-table-column prop="express_name" label="快递类型" />
								<el-table-column prop="express_no" label="快递单号" />
								<el-table-column prop="create_time" label="发货时间" />
								<el-table-column prop="remark" label="备注" />

								<el-table-column fixed="right" label="操作">
									<template #default="scope">
										<template v-if="detail.pay_status.value == 20 && detail.delivery_status.value != 10 && detail.order_status.value == 10
									  && detail.is_single == 1 && scope.row.is_label == 1 && detail.receipt_status.value == 10">
											<el-button @click="labelCancel(scope.row)" type="text" size="small"
												v-auth="'/order/order/labelCancel'">电子面单取消
											</el-button>
										</template>

										<template v-if="detail.pay_status.value == 20 && detail.delivery_status.value != 10 && detail.order_status.value == 10
									      && scope.row.label_print_type == 1
									  && detail.is_single == 1 && scope.row.is_label == 1 && detail.receipt_status.value == 10">
											<el-button @click="printOld(scope.row)" type="text" size="small"
												v-auth="'/order/order/printRepeate'">电子面单复打
											</el-button>
										</template>
										<el-button type="text" size="small" v-auth="'/order/order/express'"
											@click="getLogistics(scope.row)">物流查询
										</el-button>
									</template>
								</el-table-column>

							</el-table>
						</div>
					</div>
				</div>
				<div v-if="detail.delivery_status.value == 10 || detail.delivery_status.value == 30" class="pb50">
					<!-- 去发货 -->
					<div class="common-form mt16">去发货</div>
					<el-tabs v-model="form.is_single" v-if="detail.delivery_status.value == 10">
						<el-tab-pane label="单包裹发货" :name="1"></el-tab-pane>
						<el-tab-pane label="多包裹发货" :name="0"></el-tab-pane>
					</el-tabs>
					<el-form size="small" ref="form" :model="form" label-width="100px">
						<!-- 单包裹发货 -->
						<template v-if="form.is_single && detail.delivery_status.value == 10">
							<el-form-item label="发货类型">
								<el-radio-group v-model="form.is_label">
									<el-radio :label="0">手动填写</el-radio>
									<el-radio :label="1">电子面单打印</el-radio>
								</el-radio-group>
							</el-form-item>
							<!-- 电子面单打印 -->
							<template v-if="form.is_label">
								<el-form-item label="电子面单配置">
									<el-select size="small" v-model="form.setting_id" placeholder="请选择">
										<el-option v-for="(item, index) in label_list" :key="index"
											:label="item.setting_name" :value="item.setting_id">
										</el-option>
									</el-select>
								</el-form-item>
								<el-form-item label="电子面单模板">
									<el-select size="small" v-model="form.template_id" placeholder="请选择">
										<el-option v-for="(item, index) in template_list" :key="index"
											:label="item.template_name" :value="item.template_id">
										</el-option>
									</el-select>
								</el-form-item>
								<el-form-item label="寄件人信息">
									<el-select size="small" v-model="form.address_id" placeholder="请选择">
										<el-option v-for="(item, index) in address_list" :key="index"
											:label="item.detail" :value="item.address_id">
										</el-option>
									</el-select>
								</el-form-item>
							</template>
							<!-- 手动填写 -->
							<template v-else>
								<el-form-item label="物流公司">
									<el-select v-model="form.express_id" placeholder="请选择快递公司">
										<el-option :label="item.express_name" v-for="(item, index) in expressList"
											:key="index" :value="item.express_id"></el-option>
									</el-select>
								</el-form-item>
								<el-form-item label="物流单号"><el-input v-model="form.express_no"
										class="max-w460"></el-input></el-form-item>
							</template>
						</template>
						<!-- 多包裹发货 -->
						<template v-else>
							<div class="d-s-c mb16">
								<el-button size="small" type="primary" @click="addPackage">
									+ 添加物流单号
								</el-button>
								<div class="ml10">
									<div class="d-s-c">
										<div>多运单发货</div>
										<el-tooltip content="发货提示" placement="top">
											<el-icon class="ml4">
												<QuestionFilled />
											</el-icon>
										</el-tooltip>
									</div>
								</div>
							</div>
							<el-form-item label="发货类型">
								<el-radio-group v-model="form.is_label">
									<el-radio :label="0">手动填写</el-radio>
									<el-radio :label="1">电子面单打印</el-radio>
								</el-radio-group>
							</el-form-item>
							<template v-for="(v, idx) in form.delivery_list" :key="v">
								<div class="d-s-s mt16">
									<div class="package-pre">包裹{{ idx + 1 }}：</div>
									<div class="flex-1">
										<template v-if="form.is_label">
											<el-form-item label="电子面单配置">
												<el-select size="small" v-model="v.setting_id" placeholder="请选择">
													<el-option v-for="(item, index) in label_list" :key="index"
														:label="item.setting_name" :value="item.setting_id">
													</el-option>
												</el-select>
											</el-form-item>
											<el-form-item label="电子面单模板">
												<el-select size="small" v-model="v.template_id" placeholder="请选择">
													<el-option v-for="(item, index) in template_list" :key="index"
														:label="item.template_name" :value="item.template_id">
													</el-option>
												</el-select>
											</el-form-item>
											<el-form-item label="寄件人信息">
												<el-select size="small" v-model="v.address_id" placeholder="请选择">
													<el-option v-for="(item, index) in address_list" :key="index"
														:label="item.detail" :value="item.address_id">
													</el-option>
												</el-select>
											</el-form-item>
										</template>
										<template v-else>
											<div class="ww100">
												<el-form-item label="物流公司">
													<div class="d-s-c">
														<el-select v-model="v.express_id" placeholder="请选择快递公司">
															<el-option :label="item.express_name"
																v-for="(item, index) in expressList" :key="index"
																:value="item.express_id"></el-option>
														</el-select>
														<div class="iconfont icon-shanchu1 ml10 delete-icon"
															style="cursor: pointer" @click="delPackage(idx)"></div>
														<el-button class="ml10" size="small" type="primary" round
															@click="addPackage">+ 添加包裹</el-button>
													</div>
												</el-form-item>
											</div>
											<el-form-item label="物流单号">
												<el-input v-model="v.express_no" class="max-w460"></el-input>
											</el-form-item>
											<el-form-item label="备注">
												<el-input v-model="v.remark" class="max-w460"></el-input>
											</el-form-item>
										</template>
									</div>
								</div>
								<el-button icon="Plus" class="mb16" @click="addProduct(idx)">添加关联商品</el-button>
								<el-table :data="v.delivery_data" border>
									<el-table-column label="商品图片">
										<template #default="scope">
											<img v-img-url="scope.row.image_path" :width="60" />
										</template>
									</el-table-column>
									<el-table-column prop="product_name" label="商品名称"></el-table-column>
									<el-table-column prop="delivery_num" label="商品数量">
										<template #default="scope">
											<!-- <el-input-number v-model="scope.row.delivery_num" :min="1" :max="scope.row.maxDeliveryNum" /> -->
											<el-input-number v-model="scope.row.delivery_num" :min="1" :max="100" />
										</template>
									</el-table-column>
									<el-table-column prop="name" label="操作" width="60">
										<template #default="{ $index }">
											<div class="iconfont icon-shanchu delete-icon" style="cursor: pointer"
												@click="delProduct(v.delivery_data, $index)"></div>
										</template>
									</el-table-column>
								</el-table>
							</template>
						</template>
					</el-form>
				</div>
			</div>
			<template v-if="custom_form && custom_form.length > 0">
				<div class="common-form mt16">表单信息</div>
				<div class="table-wrap">
					<div class="table-wrap">
						<ul class="list">
							<li class="item" :span="item.label !== 'text' ? 12 : 24"
								v-for="(item, index) in custom_form" :key="index">
								<div>{{ item.title }}：{{ item.value }}</div>
							</li>
						</ul>
					</div>
				</div>
			</template>
			<!--取消信息-->
			<div class="table-wrap" v-if="detail.order_status.value == 20 && detail.cancel_remark != ''">
				<div class="common-form mt16">取消信息</div>
				<div class="table-wrap">
					<el-row>
						<el-col :span="5">
							<div class="pb16">
								<span class="gray9">商家备注：</span>
								{{ detail.cancel_remark }}
							</div>
						</el-col>
					</el-row>
				</div>
			</div>
			<!--门店自提核销-->
			<div v-if="detail.delivery_type.value == 20 && detail.pay_status.value == 20 && detail.order_status.value != 21 && detail.order_status.value != 20"
				v-auth="'/order/operate/extract'">
				<div class="common-form mt16">门店自提核销</div>
				<div v-if="detail.delivery_status.value == 10">
					<el-form size="small" ref="extract_form" :model="extract_form" label-width="100px">
						<el-form-item label="门店核销员">
							<el-select v-model="extract_form.order.extract_clerk_id" placeholder="点击选择">
								<el-option :label="item.real_name" v-for="(item, index) in shopClerkList" :key="index"
									:value="item.clerk_id"></el-option>
							</el-select>
						</el-form-item>
						<el-form-item label="买家取货状态 ">
							<el-radio v-model="extract_form.order.extract_status" :label="1">已取货</el-radio>
						</el-form-item>
						<el-form-item>
							<el-button type="primary" @click="onExtract(detail.order_id)">确认核销</el-button>
						</el-form-item>
					</el-form>
				</div>
				<div v-else class="table-wrap">
					<template v-if="detail.extractClerk">
						<el-row>
							<el-col :span="5">
								<div class="pb16">
									<span class="gray9">自提门店名称：</span>
									{{ detail.extractStore.store_name }}
								</div>
							</el-col>
							<el-col :span="5">
								<div class="pb16">
									<span class="gray9">核销员：</span>
									{{ detail.extractClerk.real_name }}
								</div>
							</el-col>
							<el-col :span="5">
								<div class="pb16">
									<span class="gray9">核销状态：</span>
									<template v-if="detail.delivery_status.value == 20">
										已核销
									</template>
								</div>
							</el-col>
							<el-col :span="5">
								<div class="pb16">
									<span class="gray9">核销时间：</span>
									{{ detail.delivery_time }}
								</div>
							</el-col>
						</el-row>
					</template>
				</div>
			</div>
			<!--虚拟物品发货-->
			<div v-if="detail.delivery_type.value == 30 && detail.pay_status.value == 20 && detail.order_status.value != 21 && detail.order_status.value != 20"
				v-auth="'/order/order/delivery'">
				<div class="common-form mt16">虚拟商品发货</div>
				<div v-if="detail.delivery_status.value == 10">
					<el-form size="small" ref="virtual_form" :model="virtual_form" label-width="100px">
						<el-form-item label="发货信息">
							<el-input v-model="virtual_form.virtual_content" class="max-w460"></el-input>
						</el-form-item>
						<el-form-item>
							<el-button type="primary" @click="onVirtual(detail.order_id)">确认发货</el-button>
						</el-form-item>
					</el-form>
				</div>
				<div v-else class="table-wrap">
					<template v-if="detail.virtual_content">
						<el-row>
							<el-col :span="5">
								<div class="pb16">
									<span class="gray9">发货信息：</span>
									{{ detail.virtual_content }}
								</div>
							</el-col>
							<el-col :span="5">
								<div class="pb16">
									<span class="gray9">发货状态：</span>
									<template v-if="detail.delivery_status.value == 20">
										已发货
									</template>
								</div>
							</el-col>
							<el-col :span="5">
								<div class="pb16">
									<span class="gray9">发货时间：</span>
									{{ detail.delivery_time }}
								</div>
							</el-col>

						</el-row>
					</template>
				</div>
			</div>
		</div>
		<div class="common-button-wrapper">
			<el-button size="small" type="info" @click="cancelFunc">返回上一页</el-button>
			<!--确认发货-->
			<template
				v-if="detail.pay_status.value == 20 && detail.delivery_type.value == 10 && [20, 21].indexOf(detail.order_status.value) === -1 && (!detail.assemble_status || detail.assemble_status == 20)">

				<template v-if="detail.delivery_status.value == 10">
					<el-button size="small" type="primary" @click="onSubmit">确认发货</el-button>
				</template>
				<template v-else-if="detail.is_single == 1 && detail.delivery_status.value != 20">
					<el-button size="small" type="primary" @click="onSubmit">继续发货</el-button>
				</template>
			</template>
			<!--用户取消-->
			<template v-if="detail.pay_status.value == 20 && detail.order_status.value == 21">
				<el-button size="small" type="primary" @click="confirmCancel()">确认审核</el-button>
			</template>
		</div>
		<changeAddress v-if="addressData!=null" :isChange="isChange" :addressData="addressData"
			@closeDialog='closeAddress'></changeAddress>
		<ProductSelect ref="ProductSelectRef" :product_list="product_list" :exclude_ids="exclude_ids" :islist="true"
			@closeDialog="closeProductDialogFunc">产品列表弹出层</ProductSelect>
		<Logistics v-if="isLogistics" :logisticsData="logisticsData" :isLogistics="isLogistics"
			@closeDialog='closeLogistics'></Logistics>
	</div>
</template>
<script>
	import OrderApi from '@/api/order.js';
	import changeAddress from '@/components/order/changeAddress.vue';
	import Add from './dialog/Add.vue';
	import {
		deepClone
	} from '@/utils/base.js';
	import ProductSelect from '@/components/product/deliveryProduct.vue';
	import Logistics from '@/components/logistics/viewLogistics.vue';
	export default {
		components: {
			Add,
			changeAddress,
			ProductSelect,
			Logistics
		},
		data() {
			return {
				active: 0,
				/*是否加载完成*/
				loading: true,
				/*订单数据*/
				detail: {
					pay_status: [],
					pay_type: [],
					delivery_type: [],
					user: {},
					address: [],
					product: [],
					order_status: [],
					extract: [],
					extract_store: [],
					express: [],
					delivery_status: [],
					extractClerk: []
				},
				/*是否打开添加弹窗*/
				open_add: false,
				/*一页多少条*/
				pageSize: 20,
				/*一共多少条数据*/
				totalDataNumber: 0,
				/*当前是第几页*/
				curPage: 1,
				/*发货*/
				form: {
					/*订单ID*/
					express_id: null,
					/*订单号*/
					express_no: '',
					is_single: 1,
					delivery_list: [],
					is_label: 0,
				},
				forms: {
					is_cancel: 1,
					is_single: 1,
				},
				extract_form: {
					order: {
						extract_status: 1
					}
				},
				/*虚拟商品发货*/
				virtual_form: {
					virtual_content: ''
				},
				order: {},
				delivery_type: 0,
				/*配送方式*/
				exStyle: [],
				/*门店列表*/
				shopList: [],
				/*当前编辑的对象*/
				userModel: {},
				/*时间*/
				create_time: '',
				/*快递公司列表*/
				expressList: [],
				shopClerkList: [],
				/*是否打开编辑弹窗*/
				open_edit: false,
				isChange: false,
				addressData: null,
				current_index: 0,
				template_list: [],
				address_list: [],
				label_list: [],
				product_list: [],
				/*商品需要去重的*/
				exclude_ids: [],
				isLogistics: false,
				logisticsData: {},
				custom_form: []
			};
		},
		created() {
			/*获取列表*/
			this.getParams();
		},
		methods: {
			getLogistics(row) {
				let self = this;
				let Params = {
					order_id: row.order_id,
					express_id: row.express_id,
					express_no: row.express_no,
				};
				self.loading = true;
				OrderApi.orderExpress(Params, true)
					.then(res => {
						self.loading = false;
						self.logisticsData = res.data.express.list;
						console.log(self.logisticsData);
						self.openLogistics();
					})
					.catch(error => {
						self.loading = false;
					});
			},
			openLogistics() {
				this.isLogistics = true;

			},
			closeLogistics() {
				this.isLogistics = false;
			},
			addPackage() {
				this.form.delivery_list = this.form.delivery_list || [];
				this.form.delivery_list.push({
					express_id: '',
					express_no: '',
					delivery_data: [],
				});
			},
			delPackage(index) {
				let delivery_data = this.form.delivery_list[index].delivery_data;
				let product_id = [];
				delivery_data.forEach((v) => {
					product_id.push(v.product_id);
				});
				this.filterExcludeIds(product_id);
				this.form.delivery_list.splice(index, 1);
			},
			addProduct(index) {
				/* 获取之前已选中的产品总共数量 { product_id:v.product_id,delivery_num: v.delivery_num } */
				let beforeDeliveryNumList = [];
				// 选中的产品id
				let product_ids = [];
				// 禁用的产品ID
				let exclude_ids = [];
				this.form.delivery_list.forEach((packageData) => {
					packageData.delivery_data.forEach((v) => {
						if (product_ids.includes(v.product_id)) {
							let currentProductIndex = beforeDeliveryNumList.findIndex(
								(product) => {
									return product.product_id == v.product_id;
								}
							);
							if (currentProductIndex != -1) {
								let currentProduct = beforeDeliveryNumList[currentProductIndex];
								let delivery_num =
									Number(currentProduct.delivery_num) + Number(v.delivery_num);
								beforeDeliveryNumList[currentProductIndex].delivery_num =
									delivery_num;
								if (v.total_num == delivery_num || v.total_num < delivery_num) {
									exclude_ids.push(v.product_id);
								}
							}
						} else {
							product_ids.push(v.product_id);
							beforeDeliveryNumList.push({
								product_id: v.product_id,
								delivery_num: v.delivery_num,
							});
							if (v.total_num == v.delivery_num || v.total_num < v.delivery_num) {
								exclude_ids.push(v.product_id);
							}
						}
					});
				});
				console.log('不可选的', exclude_ids);
				this.current_index = index;
				this.exclude_ids = exclude_ids;
				this.$refs.ProductSelectRef.open(beforeDeliveryNumList);
			},
			delProduct(row, index) {
				this.filterExcludeIds([row[index].product_id]);
				row.splice(index, 1);
			},
			labelCancel(row) {
				let self = this;
				let order_id = row.order_id;
				let is_multi = 0;
				if (row.order_delivery_id && row.order_delivery_id > 0) {
					order_id = row.order_delivery_id;
					is_multi = 1;
				}
				ElMessageBox.confirm('确定取消吗?', '提示', {
					type: 'warning'
				}).then(() => {
					OrderApi.labelCancel({
								order_id,
								is_multi
							},
							true
						)
						.then(data => {
							self.loading = false;
							ElMessage({
								message: '取消成功',
								type: 'success'
							});
							self.getParams();
						})
						.catch(error => {
							self.loading = false;
						});
				}).catch(() => {
					ElMessage({
						type: 'info',
						message: '取消失败'
					});
				});
			},
			printOld(row) {
				let self = this;
				let order_id = row.order_id;
				let is_multi = 0;
				if (row.order_delivery_id && row.order_delivery_id > 0) {
					order_id = row.order_delivery_id;
					is_multi = 1;
				}
				ElMessageBox.confirm('最多可复打10次,确定复打吗?', '提示', {
					type: 'warning'
				}).then(() => {
					OrderApi.printOld({
								order_id,
								is_multi
							},
							true
						)
						.then(data => {
							self.loading = false;
							ElMessage({
								message: '复打成功',
								type: 'success'
							});
							self.getParams();
						})
						.catch(error => {
							self.loading = false;
						});
				}).catch(() => {
					ElMessage({
						type: 'info',
						message: '复打失败'
					});
				});
			},
			filterExcludeIds(idList) {
				idList.forEach((id) => {
					let idx = this.exclude_ids.indexOf(id);
					if (idx > -1) {
						this.exclude_ids.splice(idx, 1);
					}
				});
			},
			closeProductDialogFunc(e) {
				console.log('e', e);
				let delivery_data =
					this.form.delivery_list[this.current_index].delivery_data || [];
				let checkProduct = e.params || [];
				let beforeArr = delivery_data.concat(checkProduct);
				let flagArr = [];
				let flagIds = [];
				beforeArr.forEach((v) => {
					if (flagIds.includes(v.product_id)) {
						let flagProductIdx = flagArr.findIndex((product) => {
							return product.product_id == v.product_id;
						});
						// console.log("flagProductIdx",flagProductIdx)
						if (flagProductIdx != -1) {
							flagArr[flagProductIdx].delivery_num =
								Number(flagArr[flagProductIdx].delivery_num) +
								Number(v.delivery_num);
						}
					} else {
						flagIds.push(v.product_id);
						flagArr.push(v);
					}
				});
				this.exclude_ids = this.exclude_ids.concat(e.checkIds);
				this.form.delivery_list[this.current_index].delivery_data = flagArr;
			},
			next() {
				if (this.active++ > 4) this.active = 0;
			},
			/*获取参数*/
			getParams() {
				let self = this;
				// 取到路由带过来的参数
				const params = this.$route.query.order_id;
				OrderApi.orderdetail({
							order_id: params
						},
						true
					)
					.then(data => {
						self.loading = false;
						self.detail = data.data.detail;
						self.expressList = data.data.expressList;
						self.shopClerkList = data.data.shopClerkList;
						self.addressData = self.detail.address;
						self.product_list = data.data.detail.product;
						self.template_list = data.data.template_list;
						self.address_list = data.data.address_list;
						self.custom_form = self.detail.custom_form;
						console.log(self.custom_form)
						self.label_list = data.data.label_list;
						if (self.detail.delivery_status.value == 30) {
							self.form.is_single = 0;
						}
					})
					.catch(error => {
						self.loading = false;
					});
			},

			/*发货*/
			onSubmit() {
				let self = this;
				let form = JSON.parse(JSON.stringify(self.form));
				let order_id = this.$route.query.order_id;
				let currentProduct = [];
				let product_ids = [];
				let error_tip_list = [];
				let error_tip_ids = [];
				let flag = true;
				let emptyPackage = false;
				let errorIsLabelTip = [];
				let flagLableTip = true;
				if (form.is_single) {
					if (form.is_label) {
						if (!form.setting_id || !form.template_id || !form.address_id) {
							return ElMessage.error('电子面单相关信息请填写完整');
						}
					}
				}
				if (!form.is_single) {
					form.delivery_list.forEach((items, indexs) => {
						if (items.delivery_data && items.delivery_data.length > 0) {

							let list = [];
							items.delivery_data.forEach((v) => {
								if (product_ids.includes(v.product_id)) {
									currentProduct.forEach((current) => {
										if (current.product_id == v.product_id) {
											let delivery_num =
												Number(v.delivery_num) + Number(current
													.delivery_num);
											current.delivery_num = delivery_num;
										}
										if (
											current.delivery_num > v.total_num &&
											current.product_id == v.product_id
										) {
											flag = false;
											if (!error_tip_ids.includes(v.product_id)) {
												error_tip_ids.push(v.product_id);
												error_tip_list.push({
													product_id: v.product_id,
													product_name: v.product_name,
													total_num: v.total_num,
												});
											}
										}
									});
								} else {
									product_ids.push(v.product_id);
									currentProduct.push({
										product_id: v.product_id,
										delivery_num: v.delivery_num,
										product_name: v.product_name,
									});
									if (v.delivery_num > v.total_num) {
										error_tip_list.push({
											product_id: v.product_id,
											product_name: v.product_name,
											total_num: v.total_num,
										});
										error_tip_ids.push(v.product_id);
										flag = false;
									}
								}
								list.push({
									order_product_id: v.order_product_id,
									delivery_num: v.delivery_num,
									image_path: v.image_path,
									product_name: v.product_name,
								});
							});
							items.delivery_data = list;

						} else {
							flag = false;
							emptyPackage = true;
						}
					});
				}
				if (!flagLableTip) {
					errorIsLabelTip.forEach((v) => {
						ElMessage.error(`第${v.indexs+1}个包裹信息填写不完整`);
					});
					return;
				}
				if (!flag) {
					if (emptyPackage) {
						ElMessage.error('无法添加空包裹，请选择商品');
						return;
					}
					if (error_tip_list && error_tip_list.length > 0) {
						error_tip_list.forEach((v) => {
							ElMessage.error(`${v.product_name}最多只能选择${v.total_num}个`);
						});
					}
					console.log('超过的商品', error_tip_list);
					return;
				}
				form.order_id = order_id;
				OrderApi.delivery(form,
						true
					)
					.then(data => {
						self.loading = false;
						ElMessage({
							message: '恭喜你，发货成功',
							type: 'success'
						});
						self.form = {};
						self.getParams();
					})
					.catch(error => {
						self.loading = false;
					});
			},
			closeAddress(e) {
				let self = this;
				if (e == false) {
					self.isChange = false;
					return false;
				}
				let params = e.params;
				params.order_id = self.$route.query.order_id;
				OrderApi.updateAddress(params,
						true
					)
					.then(data => {
						self.getParams();
						ElMessage({
							message: '修改成功',
							type: 'success'
						});
					})
					.catch(error => {

					});
				self.isChange = false;
			},
			/*确认取消*/
			confirmCancel() {
				let self = this;
				let order_id = this.$route.query.order_id;
				let is_cancel = self.forms.is_cancel;
				OrderApi.confirm({
							order_id: order_id,
							is_cancel: is_cancel
						},
						true
					)
					.then(data => {
						self.loading = false;
						ElMessage({
							message: '恭喜你，审核成功',
							type: 'success'
						});
						self.getParams();
					})
					.catch(error => {
						self.loading = false;
					});
			},

			/*核销*/
			onExtract(e) {
				let self = this;
				let extract_form = self.extract_form;
				extract_form.order_id = e;
				OrderApi.extract({
							extract_form
						},
						true
					)
					.then(data => {
						self.loading = false;
						ElMessage({
							message: '恭喜你，操作成功',
							type: 'success'
						});
						self.getParams();
					})
					.catch(error => {
						self.loading = false;
					});
			},

			/*虚拟商品发货*/
			onVirtual(e) {
				let self = this;
				let virtual_form = self.virtual_form;
				if (virtual_form.virtual_content == '') {
					ElMessage.error('请填写发货信息');
					return;
				}
				virtual_form.order_id = e;
				OrderApi.virtual({
							order_id: virtual_form.order_id,
							virtual_content: virtual_form.virtual_content,
						},
						true
					)
					.then(data => {
						self.loading = false;
						ElMessage({
							message: '恭喜你，操作成功',
							type: 'success'
						});
						self.getParams();
					})
					.catch(error => {
						self.loading = false;
					});
			},

			/*打开编辑*/
			editClick(item) {
				this.userModel = deepClone(item);
				this.open_edit = true;
			},

			/*关闭弹窗*/
			closeDialogFunc(e, f) {
				if (e && f == 'edit') {
					this.open_edit = e.openDialog;
					this.getParams();
				}
			},

			/*取消*/
			cancelFunc() {
				this.$router.back(-1);
			},
			changeAdd() {
				this.isChange = true;
			},

		}
	};
</script>
<style lang="scss">
	.pb100 {
		padding-bottom: 100px;
	}
</style>