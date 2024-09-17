<template>
	<div>
		<div class="common-form">
			<span>{{ curItem.name }}</span>
		</div>
		<el-form size="small" :model="curItem" label-width="100px">
			<div class="f16 gray3 form-subtitle">商品设置</div>
			<el-form-item label="商品来源：">
				<el-radio-group v-model="curItem.params.source" size="medium">
					<el-radio-button :label="'auto'">自动获取</el-radio-button>
					<el-radio-button :label="'choice'">手动选择</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<!-- 自动获取 -->
			<template v-if="curItem.params.source == 'auto'">
				<!-- 商品分类 -->
				<el-form-item label="商品分类：" v-if="CategoryList && CategoryList.length > 0">
					<el-cascader class="ww100" v-model="currCategory" ref="cascader" :options="CategoryList"
						:props="{ checkStrictly: true, children: 'child', value: 'category_id', label: 'name' }"
						@change="changeCategory"></el-cascader>
				</el-form-item>
				<!-- 商品排序 -->
				<el-form-item label="商品排序：">
					<el-radio-group v-model="curItem.params.auto.productSort" size="medium">
						<el-radio-button :label="'all'">综合</el-radio-button>
						<el-radio-button :label="'sales'">销量</el-radio-button>
						<el-radio-button :label="'price'">价格</el-radio-button>
					</el-radio-group>
				</el-form-item>
				<!-- 显示数量 -->
				<el-form-item label="显示数量："><el-input v-model="curItem.params.auto.showNum"
						class="w-auto"></el-input></el-form-item>
			</template>
			<!-- 手动选择 -->
			<template v-if="curItem.params.source == 'choice'">
				<el-form-item label="商品列表：">
					<draggable v-model="curItem.data" :options="{ draggable: '.item', animation: 500 }"
						class="choice-product-list">
						<template #item="{ element, index }">
							<div class="d-s-c f-w">
								<div class="item">
									<div class="delete-box">
										<el-icon :size="20" @click="$parent.onEditorDeleleData(index, selectedIndex)">
											<CircleCloseFilled />
										</el-icon>
									</div>
									<img v-img-url="element.image" alt="" />
								</div>
							</div>
						</template>
					</draggable>
					<div><el-button icon="Plus" @click.stop="$parent.openProduct(curItem.data, true)">选择产品</el-button>
					</div>
				</el-form-item>
			</template>
			<!--组件样式-->
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">组件样式</div>
			<!--上下边距-->
			<div class="form-item">
				<div class="form-label">上边距：</div>
				<el-slider v-model="curItem.style.paddingTop" size="small" show-input :show-input-controls="false"
					input-size="small"></el-slider>
			</div>
			<!--上下边距-->
			<div class="form-item">
				<div class="form-label">下边距：</div>
				<el-slider v-model="curItem.style.paddingBottom" size="small" show-input :show-input-controls="false"
					input-size="small"></el-slider>
			</div>
			<!--左右边距-->
			<div class="form-item">
				<div class="form-label">左右边距：</div>
				<el-slider max="15" v-model="curItem.style.paddingLeft" size="small" show-input
					:show-input-controls="false" input-size="small"></el-slider>
			</div>
			<!--上圆角-->
			<div class="form-item">
				<div class="form-label">上圆角：</div>
				<el-slider v-model="curItem.style.topRadio" size="small" show-input :show-input-controls="false"
					input-size="small"></el-slider>
			</div>
			<!--下圆角-->
			<div class="form-item">
				<div class="form-label">下圆角：</div>
				<el-slider v-model="curItem.style.bottomRadio" size="small" show-input :show-input-controls="false"
					input-size="small"></el-slider>
			</div>
			<div class="form-item" v-if="curItem.params.productName">
				<div class="form-label">商品名称：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.product_name_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.product_name_color" placeholder="透明" />
					<el-button style="margin-left: 10px;"
						@click.stop="$parent.onEditorResetColor(curItem.style, 'product_name_color', '#333333')"
						type="primary" link>
						重置
					</el-button>
				</div>
			</div>
			<div class="form-item" v-if="curItem.params.productPrice">
				<div class="form-label">销售价：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.product_price_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.product_price_color" placeholder="透明" />
					<el-button style="margin-left: 10px;"
						@click.stop="$parent.onEditorResetColor(curItem.style, 'product_price_color', '#FF4C01')"
						type="primary" link>
						重置
					</el-button>
				</div>
			</div>
			<div class="form-item" v-if="curItem.params.linePrice">
				<div class="form-label">划线价：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.line_price_color"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.line_price_color" placeholder="透明" />
					<el-button style="margin-left: 10px;"
						@click.stop="$parent.onEditorResetColor(curItem.style, 'line_price_color', '#999999')"
						type="primary" link>
						重置
					</el-button>
				</div>
			</div>
			<div class="form-item">
				<div class="form-label">底部背景：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.bgcolor"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.bgcolor" placeholder="透明" />
					<el-button style="margin-left: 10px;"
						@click.stop="$parent.onEditorResetColor(curItem.style, 'bgcolor', '#f2f2f2')" type="primary"
						link>重置</el-button>
				</div>
			</div>
			<div class="form-item">
				<div class="form-label">组件背景：</div>
				<div class="flex-1 d-s-c" style="height: 36px;">
					<el-color-picker size="default" v-model="curItem.style.background"></el-color-picker>
					<el-input class="ml10" v-model="curItem.style.background" placeholder="透明" />
					<el-button style="margin-left: 10px;"
						@click.stop="$parent.onEditorResetColor(curItem.style, 'background', '#ffffff')" type="primary"
						link>重置</el-button>
				</div>
			</div>
			<!-- 商品排序 -->
			<el-form-item label="商品排序：">
				<el-radio-group v-model="curItem.params.display" size="medium">
					<el-radio-button :label="'list'">列表平铺</el-radio-button>
					<!-- <el-radio-button :label="'slide'" :disabled="curItem.style.column == 1">横向滑动</el-radio-button> -->
				</el-radio-group>
			</el-form-item>
			<!-- 分列数量 -->
			<el-form-item label="分列数量：">
				<el-radio-group v-model="curItem.style.column" size="medium">
					<el-radio-button :label="1" :disabled="curItem.style.display == 'slide'">单列</el-radio-button>
					<el-radio-button :label="2">两列</el-radio-button>
					<!-- <el-radio-button :label="3">三列</el-radio-button> -->
				</el-radio-group>
			</el-form-item>
			<!--组件样式-->
			<div class="form-chink"></div>
			<div class="f16 gray3 form-subtitle">显示内容</div>
			<!-- 商品名称 -->
			<el-form-item label="商品名称：">
				<el-radio-group v-model="curItem.params.productName" size="medium">
					<el-radio-button :label="1">显示</el-radio-button>
					<el-radio-button :label="0">隐藏</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<!-- 商品名称 -->
			<el-form-item label="商品价格：">
				<el-radio-group v-model="curItem.params.productPrice" size="medium">
					<el-radio-button :label="1">显示</el-radio-button>
					<el-radio-button :label="0">隐藏</el-radio-button>
				</el-radio-group>
			</el-form-item>
			<!-- 商品名称 -->
			<el-form-item label="划线价格：">
				<el-radio-group v-model="curItem.params.linePrice" size="medium">
					<el-radio-button :label="1">显示</el-radio-button>
					<el-radio-button :label="0">隐藏</el-radio-button>
				</el-radio-group>
			</el-form-item>
		</el-form>
	</div>
</template>

<script>
	import ProductApi from '@/api/product.js';
	import draggable from 'vuedraggable';
	export default {
		components: {
			draggable
		},
		data() {
			return {
				/*是否正在加载*/
				loading: true,
				/*商品类别*/
				CategoryList: [],
				/*当前选中的*/
				currCategory: [],
				productNameShow: false,
				productPriceShow: false,
				linePriceShow: false,
				sellingPointShow: false,
				productSalesShow: false
			};
		},
		props: ['curItem', 'selectedIndex', 'opts'],
		created() {
			/*获取列表*/
			this.getData();
		},
		watch: {
			selectedIndex: function(n, o) {
				this.currCategory = this.currCategoryAuto(this.CategoryList);
			}
		},
		methods: {
			/*获取商品*/
			getData() {
				let self = this;
				ProductApi.catList({
							page_id: self.page_id
						},
						true
					)
					.then(res => {
						self.CategoryList = res.data.list;
						self.currCategory = self.currCategoryAuto(res.data.list);
						self.loading = false;
					})
					.catch(error => {
						self.loading = false;
					});
			},

			/*选择默认*/
			currCategoryAuto(list) {
				let arr = [];
				for (let i = 0; i < list.length; i++) {
					let item = list[i];
					if (item.category_id == this.curItem.params.auto.category) {
						arr.push(item.category_id);
						break;
					} else {
						if (Object.prototype.toString.call(item.child) == '[object Array]' && item.child.length > 0) {
							for (let j = 0; j < item.child.length; j++) {
								if (item.child[j].category_id == this.curItem.params.auto.category) {
									arr.push(item.category_id);
									arr.push(item.child[j].category_id);
									break;
								}
							}
						}
					}
				}
				return arr;
			},

			/*检查*/
			check(checked, name) {
				let value = checked ? 1 : 0;
				this.curItem.style.show[name] = value;
			},

			/*选择类别*/
			changeCategory(e) {
				let item = this.$refs['cascader'].getCheckedNodes();
				this.curItem.params.auto.category = item[0].data.category_id;
			}
		}
	};
</script>

<style lang="scss">
	.choice-product-list {
		display: flex;
		justify-content: flex-start;
		flex-wrap: wrap;
		padding: 20px 0;
	}

	.choice-product-list .item {
		position: relative;
		width: 80px;
		height: 80px;
		margin-right: 10px;
		margin-bottom: 10px;
		border: 1px solid #dddddd;
	}

	.choice-product-list .item .delete-box {
		position: absolute;
		width: 20px;
		height: 20px;
		top: -10px;
		right: -10px;
		font-size: 20px;
		cursor: pointer;
		color: #999999;
	}

	.choice-product-list .item .delete-box:hover {
		color: rgb(255, 51, 0);
	}

	.choice-product-list .item.plus-btn {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	.choice-product-list .item.plus-btn>i {
		font-size: 30px;
		color: #cccccc;
	}

	.choice-product-list img {
		width: 100%;
		height: 100%;
	}
</style>