package com.alipay.easysdk.kms.aliyun;


import com.alipay.easysdk.kernel.Config;
import com.aliyun.tea.*;

/**
 * KMS配置引數模型
 */
public class AliyunKMSConfig extends Config {
    /**
     * 阿里雲官方申請的AccessKey Id
     */
    @NameInMap("aliyunAccessKeyId")
    public String aliyunAccessKeyId;

    /**
     * 阿里雲官方申請的AccessKey Secret
     */
    @NameInMap("aliyunAccessKeySecret")
    public String aliyunAccessKeySecret;

    /**
     * 從阿里雲官方獲取的臨時安全令牌Security Token
     */
    @NameInMap("aliyunSecurityToken")
    public String aliyunSecurityToken;

    /**
     * 阿里雲RAM角色全域性資源描述符
     */
    @NameInMap("aliyunRoleArn")
    public String aliyunRoleArn;

    /**
     * 阿里雲RAM角色自定義策略
     */
    @NameInMap("aliyunRolePolicy")
    public String aliyunRolePolicy;

    /**
     * 阿里雲ECS例項RAM角色名稱
     */
    @NameInMap("aliyunRoleName")
    public String aliyunRoleName;

    /**
     * KMS主金鑰ID
     */
    @NameInMap("kmsKeyId")
    public String kmsKeyId;

    /**
     * KMS主金鑰版本ID
     */
    @NameInMap("kmsKeyVersionId")
    public String kmsKeyVersionId;

    /**
     * KMS服務地址
     * KMS服務地址列表詳情，請參考：
     * https://help.aliyun.com/document_detail/69006.html?spm=a2c4g.11186623.2.9.783f77cfAoNhY6#concept-69006-zh
     */
    @NameInMap("kmsEndpoint")
    public String kmsEndpoint;

    /**
     * 憑據型別，支援的型別有"access_key"，"sts"，"ecs_ram_role"，"ram_role_arn"
     */
    @NameInMap("credentialType")
    public String credentialType;
}
