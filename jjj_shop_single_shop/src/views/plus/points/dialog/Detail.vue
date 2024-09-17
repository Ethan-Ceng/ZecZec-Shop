<template>
  <el-dialog title="商品规格" v-model="dialogVisible" @close='dialogFormVisible' :close-on-click-modal="false"
    :close-on-press-escape="false" width="600px">
    <el-form size="small" ref="form" :model="form1" label-width="250px" :loading="loading">
      <el-form-item label="" label-width="20px">
        <el-table :data="productDetail.specData.spec_list" :span-method="objectSpanMethod" border style="width: 100%; margin-top: 20px">
          <el-table-column width="180" :label="item.group_name" :key="item.group_name" v-for="(item, index) in productDetail.specData.spec_attr">
            <template #default="scope">
              {{scope.row.rows[index].spec_value}}
            </template>
          </el-table-column>
          <el-table-column label="兑换积分">
            <template #default="scope">
              <el-form-item label="" style="margin-bottom: 0;">
                <el-input size="small" type="number" v-model="scope.row.spec_form.point_num"  :disabled="true"></el-input>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column label="兑换金额">
            <template #default="scope">
              <el-form-item label="" style="margin-bottom: 0;">
                <el-input size="small" type="number" v-model="scope.row.spec_form.point_money"  :disabled="true"></el-input>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column label="活动库存">
            <template #default="scope">
              <el-form-item label="" style="margin-bottom: 0;">
                <el-input size="small" type="number" v-model="scope.row.spec_form.stock"  :disabled="true"></el-input>
              </el-form-item>
            </template>
          </el-table-column>
        </el-table>
      </el-form-item>


    </el-form>



  </el-dialog>
</template>

<script>
  import PointProductApi from '@/api/pointproduct.js';
  export default {
    data() {
      return {
        form1: {
          deduct_stock_type: 10,
          status: 0,
          product_id: 0,
          sort: 1,
        },
        /*左边长度*/
        formLabelWidth: '120px',
        /*是否显示*/
        loading: false,
        dialogVisible: false,
        point_product_id: 0,
        /*商品详情*/
        productDetail: {
          spec_type: 10,
          sku: {},
          specData: {}
        },
      };
    },
    props: ['open_edit', 'userModel'],
    created() {

      this.dialogVisible = this.open_edit;
      this.point_product_id = this.userModel.point_product_id;


      /*获取列表*/
      this.getData();
    },
    methods: {

      getData() {
        let self = this;
        let point_product_id = self.point_product_id;
        PointProductApi.getProductData({
            point_product_id: point_product_id
          }, true)
          .then(data => {
            self.form1 = data.data.data;
            self.form1.product_id = data.data.data.product_id;
            self.productDetail.spec_type = self.form1.product.spec_type;
            self.productDetail.specData = data.data.specData;

            // self.form1 = data.data.data;
            // self.productDetail.spec_type = self.form1.product.spec_type;
            // self.productDetail.specData = data.data.specData;

            self.buildSkulist();

          })
          .catch(error => {
            self.loading = false;
          });
      },


      /**
       * 构建规格组合列表
       */
      buildSkulist: function() {
        var self = this;
        var spec_attr = self.productDetail.specData.spec_attr;
        var specArr = [];
        for (var i = 0; i < spec_attr.length; i++) {
          specArr.push(spec_attr[i].spec_items);
        }
        var specListTemp = self.calcDescartes(specArr);

        var specList = [];
        for (var i = 0; i < specListTemp.length; i++) {
          var rows = [];
          var specSkuIdAttr = [];
          if (!Array.isArray(specListTemp[i])) {
            rows.push(specListTemp[i]);
          } else {
            rows = specListTemp[i];
          }
          for (var j = 0; j < rows.length; j++) {
            specSkuIdAttr.push(rows[j].item_id);
          }
          specList.push({
            spec_sku_id: specSkuIdAttr.join('_'),
            rows: rows,
            spec_form: {}
          });
        }

        // 合并旧sku数据
        if (self.productDetail.specData.spec_list.length > 0 && specList.length > 0) {
          for (i = 0; i < specList.length; i++) {
            var overlap = self.productDetail.specData.spec_list.filter(function(val) {
              return val.spec_sku_id === specList[i].spec_sku_id;
            });
            if (overlap.length > 0) {
              specList[i].spec_form = overlap[0].spec_form;
              specList[i].spec_form.product_sku_id = overlap[0].product_sku_id;
            }
            overlap = self.form1.sku.filter(function(val) {
              return val.product_sku_id === specList[i].spec_form.product_sku_id;
            });
            if (overlap.length > 0) {
              specList[i].spec_form.point_money = overlap[0].point_money;
              specList[i].spec_form.point_num = overlap[0].point_num;
              specList[i].spec_form.stock = overlap[0].stock;
              specList[i].spec_form.point_product_sku_id = overlap[0].point_product_sku_id;
            }
          }
        }
        self.productDetail.specData.spec_list = specList;

      },
      calcDescartes: function(array) {
        if (array.length < 2) return array[0] || [];
        return [].reduce.call(array, function(col, set) {
          var res = [];
          col.forEach(function(c) {
            set.forEach(function(s) {
              var t = [].concat(Array.isArray(c) ? c : [c]);
              t.push(s);
              res.push(t);
            });
          });
          return res;
        });
      },

      objectSpanMethod({
        row,
        column,
        rowIndex,
        columnIndex
      }) {
        var spec_attr = this.productDetail.specData.spec_attr;
        // 规格组合总数 (table行数)
        var totalRow = 1;
        //比如2个规格,只有第一列有多行
        if (columnIndex < spec_attr.length - 1) {
          let startRowIndex = columnIndex + 1;
          let endRowIndex = spec_attr.length - 1;
          for (var i = startRowIndex; i <= endRowIndex; i++) {
            totalRow *= spec_attr[i].spec_items.length;
          }
          if (rowIndex % totalRow === 0) {
            return {
              rowspan: totalRow,
              colspan: 1
            };
          } else {
            return {
              rowspan: 0,
              colspan: 0
            };
          }
        }
      },

      /*关闭弹窗*/
      dialogFormVisible(e) {
        if (e) {
          this.$emit('closeDialog', {
            type: 'success',
            openDialog: false
          })
        } else {
          this.$emit('closeDialog', {
            type: 'error',
            openDialog: false
          })
        }
      }

    }
  };
</script>

<style></style>
