<template>
  <div class="active-basic">
    <div class="common-form">字段信息</div>
    <el-form-item label="字段管理" prop="tableData">
      <div class="ww100">
        <el-button icon="Plus" @click="addField">添加字段</el-button>
      </div>
      <div class="pt16">
        <div class="d-a-c border-t border-l border-r">
          <div style="width: 40px;" class="d-c-c border-r">排序</div>
          <div style="min-width: 200px;" class="flex-1 d-c-c border-r">名称</div>
          <div style="width: 200px;" class="d-c-c border-r">类型</div>
          <div style="min-width: 200px;"  class="flex-1 d-c-c border-r">备选值(以英文逗号隔开)</div>
          <div style="width: 40px;" class="d-c-c border-r">必填</div>
          <div style="width: 200px;" class="d-c-c border-r">验证类型</div>
          <div style="width: 80px;" class="d-c-c">操作</div>
        </div>
        <draggable class="wrapper border-b" v-model="form.tableData"> 
          <template #item="{ element, index }">
            <div class="d-a-c border-t border-l border-r">
              <div class="icon iconfont icon-tuodong d-c-c p6 border-r" style="width: 40px;"></div>
              <div style="min-width: 200px;"  class="flex-1 p6 border-r d-c-c">
                <el-input type="text" v-model="element.name"></el-input>
              </div>
              <div style="width: 200px;" class="d-c-c p6 border-r">
                <el-select v-model="element.type" placeholder="请选择">
                  <el-option v-for="(fitem, findex) in fields" :key="findex" :label="fitem.name" :value="fitem.type">
                  </el-option>
                </el-select>
              </div>
              <div style="min-width: 200px;"  class="flex-1 p6 d-c-c border-r">
                <el-input type="text" v-model="element.select_value"
                  :disabled="element.type !='radio' && element.type !='radio-group' && element.type !='select'">
                </el-input>
              </div>
              <div style="width: 40px;" class="d-c-c p6 border-r">
                <el-checkbox v-model="element.is_required"></el-checkbox>
              </div>
              <div style="width: 200px;" class="d-c-c p6 border-r">
                <el-select v-model="element.rule" placeholder="请选择">
                  <el-option v-for="(ritem, rindex) in rules" :key="rindex" :label="ritem.name" :value="ritem.type">
                  </el-option>
                </el-select>
              </div>
              <div style="width: 80px;" class="d-c-c p6">
                <el-button type="text" size="small" @click.stop="deleteItem(index)">删除</el-button>
              </div>
            </div>
          </template>
        </draggable>
        <!--<el-table size="small" :data="form.tableData" border style="width: 100%;">
          <el-table-column label="排序" width="80">
            <template #default="scope">
              <el-button type="text" size="small">拖动排序</el-button>
            </template>
          </el-table-column>
          </transition-group>
          </draggable>
          <el-table-column label="字段名称">
            <template #default="scope">
              <el-input type="text" v-model="scope.row.name"></el-input>
            </template>
          </el-table-column>
          <el-table-column label="字段类型">
            <template #default="scope">
              <el-select v-model="scope.row.type" placeholder="请选择">
                <el-option v-for="(item, index) in fields" :key="index" :label="item.name" :value="item.type">
                </el-option>
              </el-select>
            </template>
          </el-table-column>
          <el-table-column label="备选值(以英文逗号隔开)" width="200">
            <template #default="scope">
              <el-input type="text" v-model="scope.row.product_name"
                :disabled="scope.row.type !='radio' && scope.row.type !='radio-group' && scope.row.type !='select'">
              </el-input>
            </template>
          </el-table-column>
          <el-table-column label="是否必填">
            <template #default="scope">
              <el-checkbox v-model="scope.row.is_required"></el-checkbox>
            </template>
          </el-table-column>
          <el-table-column label="验证类型">
            <template #default="scope">
              <el-select v-model="scope.row.rule" placeholder="请选择">
                <el-option v-for="(item, index) in rules" :key="index" :label="item.name" :value="item.type">
                </el-option>
              </el-select>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="50">
            <template #default="scope">
              <el-button type="text" size="small">删除</el-button>
            </template>
          </el-table-column>
        </el-table>-->
      </div>
    </el-form-item>
  </div>
</template>

<script>
  import draggable from 'vuedraggable';
  export default {
    components: {
      draggable
    },
    props: {
      form: Object
    },
    data() {
      return {
        fields: [{
            name: '单行文本',
            type: 'text'
          },
          {
            name: '多行文本',
            type: 'textarea'
          },
          {
            name: '单选',
            type: 'radio'
          },
          {
            name: '多选',
            type: 'radio-group'
          },
          {
            name: '下拉框',
            type: 'select'
          },
          {
            name: '图片',
            type: 'image'
          },
          {
            name: '地区选择',
            type: 'area'
          },
          {
            name: '时间选择',
            type: 'time'
          },
          {
            name: '日期选择',
            type: 'date'
          }
        ],
        rules: [{
            name: '不验证',
            type: 'no'
          },
          {
            name: '数字',
            type: 'number'
          },
          {
            name: '手机号码',
            type: 'mobile'
          },
          {
            name: '座机或手机号码',
            type: 'phone'
          },
          {
            name: '电子邮箱',
            type: 'email'
          },
          {
            name: '身份证',
            type: 'idcard'
          }
        ]
      };
    },
    methods: {
      addField() {
        this.form.tableData.push({
          name: '',
          type: 'text',
          is_required: true,
          rule: 'no'
        });
      },
      deleteItem(index){
        this.form.tableData.splice(index, 1);
      },
    }
  };
</script>

<style>
  .mb8{
    margin-bottom: 8px;
  }
  .p6{
   padding: 6px;
  }
</style>
