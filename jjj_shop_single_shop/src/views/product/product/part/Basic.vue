<template>
  <div class="basic-setting-content pl16 pr16">
    <!--基本信息-->
    <div class="common-form">基本信息</div>
    <el-form-item label="商品名称：" :rules="[{ required: true, message: '请填写商品名称' }]" prop="model.product_name">
      <el-input v-model="form.model.product_name" class="max-w460"></el-input>
    </el-form-item>
    <el-form-item label="商品编码：" :rules="[{ required: true, message: '请填写商品编码' }]" prop="model.product_no">
      <el-input
          v-model="form.model.product_no" class="max-w460"></el-input>
    </el-form-item>
    <el-form-item label="所属分类：" :rules="[{ required: true, message: '你选择商品分类' }]" prop="model.category_id">
      <el-select v-model="form.model.category_id" class="max-w460">
        <template v-for="cat in form.category" :key="cat.category_id">
          <el-option :value="cat.category_id" :label="cat.name"></el-option>
          <template v-if="cat.child !== undefined">
            <template v-for="two in cat.child" :key="two.category_id">
              <el-option :value="two.category_id" :label="two.name" style="padding-left: 30px;"></el-option>
              <template v-if="two.child !== undefined">
                <template v-for="three in two.child" :key="three.category_id">
                  <el-option :value="three.category_id" :label="three.name" style="padding-left: 60px;"></el-option>
                </template>
              </template>
            </template>
          </template>
        </template>
      </el-select>
    </el-form-item>
    <el-form-item label="销售状态：">
      <el-radio-group v-model="form.model.product_status">
        <el-radio :label="10">立即上架</el-radio>
        <el-radio :label="20">放入仓库</el-radio>
      </el-radio-group>
    </el-form-item>
    <el-form-item label="预告商品：">
      <el-radio-group v-model="form.model.is_preview">
        <el-radio :label="1">开启</el-radio>
        <el-radio :label="0">关闭</el-radio>
      </el-radio-group>
    </el-form-item>
    <el-form-item v-if="form.model.is_preview==1" label="预告开启购买时间"
                  :rules="[{ required: true, message: '请选择开启购买时间' }]"
                  prop="model.preview_time">
      <el-date-picker v-model="form.model.preview_time" type="datetime" value-format="YYYY-MM-DD hh:mm:ss"
                      placeholder="选择日期" width="460px">
      </el-date-picker>
    </el-form-item>
    <el-form-item label="商品图片：" :rules="[{ required: true, message: '请上传商品图片' }]" prop="model.image">
      <div class="draggable-list">
        <template v-if="form.model.image&&form.model.image.length>0">
          <draggable v-model="form.model.image" group="people" item-key="id" class="draggable-list">
            <template #item="{ element, index }">
              <div class="item">
                <img v-img-url="element.file_path"/>
                <a href="javascript:void(0);" class="delete-btn" @click.stop="deleteImg(index)">
                  <el-icon>
                    <Close/>
                  </el-icon>
                </a>
              </div>
            </template>
          </draggable>
        </template>
        <div class="item img-select" @click="openProductUpload('image', 'image')">
          <el-icon>
            <Plus/>
          </el-icon>
        </div>
      </div>
      <div class="gray">建议图片上传尺寸为800px*800px</div>
    </el-form-item>
    <el-form-item label="商品视频：">
      <el-row>
        <div class="draggable-list">
          <div class="item img-select" v-if="form.model.video_id==0" @click="openProductUpload('video', 'video')">
            <el-icon>
              <Plus/>
            </el-icon>
          </div>
          <div v-if="form.model.video_id!=0">
            <video width="150" height="150" :src="form.model.video.file_path" :autoplay="false" controls>
              您的浏览器不支持 video 标签
            </video>
            <div>
              <el-button icon="Picture" @click="delVideo">删除视频</el-button>
            </div>
          </div>
        </div>

      </el-row>
      <div class="gray">视频宽高比16:9</div>
    </el-form-item>
    <el-form-item label="视频封面：">
      <el-row>
        <div class="draggable-list">
          <div class="item img-select" v-if="form.model.poster_id==0" @click="openProductUpload('image', 'poster')">
            <el-icon>
              <Plus/>
            </el-icon>
          </div>
          <div v-if="form.model.poster_id!=0" class="item" @click="openProductUpload('image', 'poster')">
            <img :src="form.model.poster.file_path" width="100" height="100"/>
            <a href="javascript:void(0);" class="delete-btn" @click.stop="deleteVideo()">
              <el-icon>
                <Close/>
              </el-icon>
            </a>
          </div>
        </div>
      </el-row>
      <div class="gray">建议视频封面上传尺寸为800px*800px</div>
    </el-form-item>
    <el-form-item label="商品卖点：">
      <el-input
          v-model="form.model.selling_point"
          type="textarea"
          :autosize="{ minRows: 4, maxRows: 8 }"
          class="max-w460"
          show-word-limit
      />
    </el-form-item>

    <el-form-item label="商品大分类：" :rules="[{ required: true, message: '请选择商品大分类' }]">
      <el-radio-group v-model="form.model.type">
        <el-radio :label="1">群眾集資</el-radio>
        <el-radio :label="2">預購式專案</el-radio>
        <el-radio :label="3">訂閱式專案</el-radio>
        <el-radio :label="4">再登場專案</el-radio>
      </el-radio-group>
    </el-form-item>
    <el-form-item label="商品起始时间" :rules="[{ required: true, message: '请选择商品起始时间' }]">
      <el-date-picker v-model="form.model.active_time" type="datetimerange" align="right" unlink-panels
                      value-format="YYYY-MM-DD HH:mm:ss" range-separator="至" start-placeholder="开始日期"
                      end-placeholder="结束日期"
                      :picker-options="pickerOptions0"></el-date-picker>
    </el-form-item>
    <el-form-item label="目标金额：">
      <el-input type="number" min="0" v-model="form.model.target_money" class="max-w460"></el-input>
    </el-form-item>
    <el-form-item label="计划内订单总金额：">
      <el-input type="number" min="0" v-model="form.model.total_money" class="max-w460"></el-input>
    </el-form-item>

    <!--商品图片组件-->
    <Upload v-if="isProductUpload" :config="config" :isupload="isProductUpload" @returnImgs="returnProductImgsFunc">
      上传图片
    </Upload>
  </div>
</template>

<script>
import Upload from '@/components/file/Upload.vue';
import draggable from 'vuedraggable';

export default {
  components: {
    Upload,
    draggable
  },
  data() {
    return {
      isProductUpload: false,
      config: {},
      file_name: 'image',
      customBtn: false,
      CustomList: [{
        value: "text",
        label: "文本框",
      },
        {
          value: "number",
          label: "数字",
        },
        {
          value: "email",
          label: "邮件",
        },
        {
          value: "data",
          label: "日期",
        },
        {
          value: "time",
          label: "时间",
        },
        {
          value: "id",
          label: "身份证",
        },
        {
          value: "phone",
          label: "手机号",
        }
      ],
    };
  },
  inject: ['form'],
  created() {
    if (this.form.model.custom_form && this.form.model.custom_form.length != 0) {
      this.customBtn = true;
    }
  },
  methods: {
    customMessBtn(e) {
      if (!e) {
        this.form.model.custom_form = [];
      }
    },
    addcustom() {
      if (!this.form.model.custom_form) {
        this.form.model.custom_form = [];
      }
      if (this.form.model.custom_form && this.form.model.custom_form.length > 9) {
        ElMessage.warning("最多添加10条");
      } else {
        this.form.model.custom_form.push({
          title: "",
          label: "text",
          value: "",
          status: false,
        });
      }
    },
    delcustom(index) {
      this.form.model.custom_form.splice(index, 1);
    },
    /*打开上传图片*/
    openProductUpload: function (file_type, file_name) {
      this.file_name = file_name;
      if (file_type == 'image') {
        this.config = {
          total: 9,
          file_type: 'image'
        };
      } else {
        this.config = {
          total: 1,
          file_type: 'video'
        };
      }
      this.isProductUpload = true;
    },

    /*上传商品图片*/
    returnProductImgsFunc(e) {
      console.log(e);
      if (e != null) {
        if (this.file_name == 'video') {
          this.form.model.video_id = e[0].file_id;
          this.form.model.video = e[0];
        } else if (this.file_name == 'image') {
          let imgs = this.form.model.image.concat(e);
          this.form.model.image = imgs;
        } else if (this.file_name == 'poster') {
          this.form.model.poster_id = e[0].file_id;
          this.form.model.poster = e[0];
        }
      }
      this.isProductUpload = false;
    },

    /*删除商品图片*/
    deleteImg(index) {
      this.form.model.image.splice(index, 1);
    },
    delVideo() {
      this.form.model.video_id = 0;
      this.form.model.video = {};
    },
    deleteVideo() {
      this.form.model.poster_id = 0;
      this.form.model.poster = {};
    },
  }
};
</script>
<style lang="scss" scoped>
.addCustom_content {
  margin-top: 20px;

  .custom_box {
    margin-bottom: 10px;
  }
}

.addCustomBox {
  margin-top: 12px;
  font-size: 13px;
  font-weight: 400;
  color: var(--prev-color-primary);

  .btn {
    cursor: pointer;
    width: max-content;
    background-color: rgba(64, 149, 229, 1);
    color: rgba(255, 255, 255, 1);
  }
}

.titTip {
  display: inline-bolck;
  font-size: 12px;
  line-height: 24px;
  font-weight: 400;
  color: #999999;
}

.addfont {
  display: inline-block;
  font-size: 12px;
  font-weight: 400;
  color: #4095e5;
  margin-left: 14px;
  cursor: pointer;
}
</style>