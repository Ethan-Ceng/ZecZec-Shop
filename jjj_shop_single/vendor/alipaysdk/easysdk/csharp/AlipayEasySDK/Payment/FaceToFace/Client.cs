// This file is auto-generated, don't edit it. Thanks.

using System;
using System.Collections;
using System.Collections.Generic;
using System.IO;
using System.Threading.Tasks;

using Tea;
using Tea.Utils;

using Alipay.EasySDK.Payment.FaceToFace.Models;

namespace Alipay.EasySDK.Payment.FaceToFace
{
    public class Client 
    {
        protected Alipay.EasySDK.Kernel.Client _kernel;

        public Client(Alipay.EasySDK.Kernel.Client kernel)
        {
            this._kernel = kernel;
        }

        public AlipayTradePayResponse Pay(string subject, string outTradeNo, string totalAmount, string authCode)
        {
            Dictionary<string, object> runtime_ = new Dictionary<string, object>
            {
                {"ignoreSSL", this._kernel.GetConfig("ignoreSSL")},
                {"httpProxy", this._kernel.GetConfig("httpProxy")},
                {"connectTimeout", 15000},
                {"readTimeout", 15000},
                {"retry", new Dictionary<string, int?>
                {
                    {"maxAttempts", 0},
                }},
            };

            TeaRequest _lastRequest = null;
            Exception _lastException = null;
            long _now = System.DateTime.Now.Millisecond;
            int _retryTimes = 0;
            while (TeaCore.AllowRetry((IDictionary) runtime_["retry"], _retryTimes, _now))
            {
                if (_retryTimes > 0)
                {
                    int backoffTime = TeaCore.GetBackoffTime((IDictionary)runtime_["backoff"], _retryTimes);
                    if (backoffTime > 0)
                    {
                        TeaCore.Sleep(backoffTime);
                    }
                }
                _retryTimes = _retryTimes + 1;
                try
                {
                    TeaRequest request_ = new TeaRequest();
                    Dictionary<string, string> systemParams = new Dictionary<string, string>
                    {
                        {"method", "alipay.trade.pay"},
                        {"app_id", this._kernel.GetConfig("appId")},
                        {"timestamp", this._kernel.GetTimestamp()},
                        {"format", "json"},
                        {"version", "1.0"},
                        {"alipay_sdk", this._kernel.GetSdkVersion()},
                        {"charset", "UTF-8"},
                        {"sign_type", this._kernel.GetConfig("signType")},
                        {"app_cert_sn", this._kernel.GetMerchantCertSN()},
                        {"alipay_root_cert_sn", this._kernel.GetAlipayRootCertSN()},
                    };
                    Dictionary<string, object> bizParams = new Dictionary<string, object>
                    {
                        {"subject", subject},
                        {"out_trade_no", outTradeNo},
                        {"total_amount", totalAmount},
                        {"auth_code", authCode},
                        {"scene", "bar_code"},
                    };
                    Dictionary<string, string> textParams = new Dictionary<string, string>(){};
                    request_.Protocol = this._kernel.GetConfig("protocol");
                    request_.Method = "POST";
                    request_.Pathname = "/gateway.do";
                    request_.Headers = new Dictionary<string, string>
                    {
                        {"host", this._kernel.GetConfig("gatewayHost")},
                        {"content-type", "application/x-www-form-urlencoded;charset=utf-8"},
                    };
                    request_.Query = this._kernel.SortMap(TeaConverter.merge<string>
                    (
                        new Dictionary<string, string>()
                        {
                            {"sign", this._kernel.Sign(systemParams, bizParams, textParams, this._kernel.GetConfig("merchantPrivateKey"))},
                        },
                        systemParams,
                        textParams
                    ));
                    request_.Body = TeaCore.BytesReadable(this._kernel.ToUrlEncodedRequestBody(bizParams));
                    _lastRequest = request_;
                    TeaResponse response_ = TeaCore.DoAction(request_, runtime_);

                    Dictionary<string, object> respMap = this._kernel.ReadAsJson(response_, "alipay.trade.pay");
                    if (this._kernel.IsCertMode())
                    {
                        if (this._kernel.Verify(respMap, this._kernel.ExtractAlipayPublicKey(this._kernel.GetAlipayCertSN(respMap))))
                        {
                            return TeaModel.ToObject<AlipayTradePayResponse>(this._kernel.ToRespModel(respMap));
                        }
                    }
                    else
                    {
                        if (this._kernel.Verify(respMap, this._kernel.GetConfig("alipayPublicKey")))
                        {
                            return TeaModel.ToObject<AlipayTradePayResponse>(this._kernel.ToRespModel(respMap));
                        }
                    }
                    throw new TeaException(new Dictionary<string, string>
                    {
                        {"message", "驗籤失敗，請檢查支付寶公鑰設定是否正確。"},
                    });
                }
                catch (Exception e)
                {
                    if (TeaCore.IsRetryable(e))
                    {
                        _lastException = e;
                        continue;
                    }
                    throw e;
                }
            }

            throw new TeaUnretryableException(_lastRequest, _lastException);
        }

        public async Task<AlipayTradePayResponse> PayAsync(string subject, string outTradeNo, string totalAmount, string authCode)
        {
            Dictionary<string, object> runtime_ = new Dictionary<string, object>
            {
                {"ignoreSSL", this._kernel.GetConfig("ignoreSSL")},
                {"httpProxy", this._kernel.GetConfig("httpProxy")},
                {"connectTimeout", 15000},
                {"readTimeout", 15000},
                {"retry", new Dictionary<string, int?>
                {
                    {"maxAttempts", 0},
                }},
            };

            TeaRequest _lastRequest = null;
            Exception _lastException = null;
            long _now = System.DateTime.Now.Millisecond;
            int _retryTimes = 0;
            while (TeaCore.AllowRetry((IDictionary) runtime_["retry"], _retryTimes, _now))
            {
                if (_retryTimes > 0)
                {
                    int backoffTime = TeaCore.GetBackoffTime((IDictionary)runtime_["backoff"], _retryTimes);
                    if (backoffTime > 0)
                    {
                        TeaCore.Sleep(backoffTime);
                    }
                }
                _retryTimes = _retryTimes + 1;
                try
                {
                    TeaRequest request_ = new TeaRequest();
                    Dictionary<string, string> systemParams = new Dictionary<string, string>
                    {
                        {"method", "alipay.trade.pay"},
                        {"app_id", this._kernel.GetConfig("appId")},
                        {"timestamp", this._kernel.GetTimestamp()},
                        {"format", "json"},
                        {"version", "1.0"},
                        {"alipay_sdk", this._kernel.GetSdkVersion()},
                        {"charset", "UTF-8"},
                        {"sign_type", this._kernel.GetConfig("signType")},
                        {"app_cert_sn", this._kernel.GetMerchantCertSN()},
                        {"alipay_root_cert_sn", this._kernel.GetAlipayRootCertSN()},
                    };
                    Dictionary<string, object> bizParams = new Dictionary<string, object>
                    {
                        {"subject", subject},
                        {"out_trade_no", outTradeNo},
                        {"total_amount", totalAmount},
                        {"auth_code", authCode},
                        {"scene", "bar_code"},
                    };
                    Dictionary<string, string> textParams = new Dictionary<string, string>(){};
                    request_.Protocol = this._kernel.GetConfig("protocol");
                    request_.Method = "POST";
                    request_.Pathname = "/gateway.do";
                    request_.Headers = new Dictionary<string, string>
                    {
                        {"host", this._kernel.GetConfig("gatewayHost")},
                        {"content-type", "application/x-www-form-urlencoded;charset=utf-8"},
                    };
                    request_.Query = this._kernel.SortMap(TeaConverter.merge<string>
                    (
                        new Dictionary<string, string>()
                        {
                            {"sign", this._kernel.Sign(systemParams, bizParams, textParams, this._kernel.GetConfig("merchantPrivateKey"))},
                        },
                        systemParams,
                        textParams
                    ));
                    request_.Body = TeaCore.BytesReadable(this._kernel.ToUrlEncodedRequestBody(bizParams));
                    _lastRequest = request_;
                    TeaResponse response_ = await TeaCore.DoActionAsync(request_, runtime_);

                    Dictionary<string, object> respMap = await this._kernel.ReadAsJsonAsync(response_, "alipay.trade.pay");
                    if (this._kernel.IsCertMode())
                    {
                        if (this._kernel.Verify(respMap, this._kernel.ExtractAlipayPublicKey(this._kernel.GetAlipayCertSN(respMap))))
                        {
                            return TeaModel.ToObject<AlipayTradePayResponse>(this._kernel.ToRespModel(respMap));
                        }
                    }
                    else
                    {
                        if (this._kernel.Verify(respMap, this._kernel.GetConfig("alipayPublicKey")))
                        {
                            return TeaModel.ToObject<AlipayTradePayResponse>(this._kernel.ToRespModel(respMap));
                        }
                    }
                    throw new TeaException(new Dictionary<string, string>
                    {
                        {"message", "驗籤失敗，請檢查支付寶公鑰設定是否正確。"},
                    });
                }
                catch (Exception e)
                {
                    if (TeaCore.IsRetryable(e))
                    {
                        _lastException = e;
                        continue;
                    }
                    throw e;
                }
            }

            throw new TeaUnretryableException(_lastRequest, _lastException);
        }

        public AlipayTradePrecreateResponse PreCreate(string subject, string outTradeNo, string totalAmount)
        {
            Dictionary<string, object> runtime_ = new Dictionary<string, object>
            {
                {"ignoreSSL", this._kernel.GetConfig("ignoreSSL")},
                {"httpProxy", this._kernel.GetConfig("httpProxy")},
                {"connectTimeout", 15000},
                {"readTimeout", 15000},
                {"retry", new Dictionary<string, int?>
                {
                    {"maxAttempts", 0},
                }},
            };

            TeaRequest _lastRequest = null;
            Exception _lastException = null;
            long _now = System.DateTime.Now.Millisecond;
            int _retryTimes = 0;
            while (TeaCore.AllowRetry((IDictionary) runtime_["retry"], _retryTimes, _now))
            {
                if (_retryTimes > 0)
                {
                    int backoffTime = TeaCore.GetBackoffTime((IDictionary)runtime_["backoff"], _retryTimes);
                    if (backoffTime > 0)
                    {
                        TeaCore.Sleep(backoffTime);
                    }
                }
                _retryTimes = _retryTimes + 1;
                try
                {
                    TeaRequest request_ = new TeaRequest();
                    Dictionary<string, string> systemParams = new Dictionary<string, string>
                    {
                        {"method", "alipay.trade.precreate"},
                        {"app_id", this._kernel.GetConfig("appId")},
                        {"timestamp", this._kernel.GetTimestamp()},
                        {"format", "json"},
                        {"version", "1.0"},
                        {"alipay_sdk", this._kernel.GetSdkVersion()},
                        {"charset", "UTF-8"},
                        {"sign_type", this._kernel.GetConfig("signType")},
                        {"app_cert_sn", this._kernel.GetMerchantCertSN()},
                        {"alipay_root_cert_sn", this._kernel.GetAlipayRootCertSN()},
                    };
                    Dictionary<string, object> bizParams = new Dictionary<string, object>
                    {
                        {"subject", subject},
                        {"out_trade_no", outTradeNo},
                        {"total_amount", totalAmount},
                    };
                    Dictionary<string, string> textParams = new Dictionary<string, string>(){};
                    request_.Protocol = this._kernel.GetConfig("protocol");
                    request_.Method = "POST";
                    request_.Pathname = "/gateway.do";
                    request_.Headers = new Dictionary<string, string>
                    {
                        {"host", this._kernel.GetConfig("gatewayHost")},
                        {"content-type", "application/x-www-form-urlencoded;charset=utf-8"},
                    };
                    request_.Query = this._kernel.SortMap(TeaConverter.merge<string>
                    (
                        new Dictionary<string, string>()
                        {
                            {"sign", this._kernel.Sign(systemParams, bizParams, textParams, this._kernel.GetConfig("merchantPrivateKey"))},
                        },
                        systemParams,
                        textParams
                    ));
                    request_.Body = TeaCore.BytesReadable(this._kernel.ToUrlEncodedRequestBody(bizParams));
                    _lastRequest = request_;
                    TeaResponse response_ = TeaCore.DoAction(request_, runtime_);

                    Dictionary<string, object> respMap = this._kernel.ReadAsJson(response_, "alipay.trade.precreate");
                    if (this._kernel.IsCertMode())
                    {
                        if (this._kernel.Verify(respMap, this._kernel.ExtractAlipayPublicKey(this._kernel.GetAlipayCertSN(respMap))))
                        {
                            return TeaModel.ToObject<AlipayTradePrecreateResponse>(this._kernel.ToRespModel(respMap));
                        }
                    }
                    else
                    {
                        if (this._kernel.Verify(respMap, this._kernel.GetConfig("alipayPublicKey")))
                        {
                            return TeaModel.ToObject<AlipayTradePrecreateResponse>(this._kernel.ToRespModel(respMap));
                        }
                    }
                    throw new TeaException(new Dictionary<string, string>
                    {
                        {"message", "驗籤失敗，請檢查支付寶公鑰設定是否正確。"},
                    });
                }
                catch (Exception e)
                {
                    if (TeaCore.IsRetryable(e))
                    {
                        _lastException = e;
                        continue;
                    }
                    throw e;
                }
            }

            throw new TeaUnretryableException(_lastRequest, _lastException);
        }

        public async Task<AlipayTradePrecreateResponse> PreCreateAsync(string subject, string outTradeNo, string totalAmount)
        {
            Dictionary<string, object> runtime_ = new Dictionary<string, object>
            {
                {"ignoreSSL", this._kernel.GetConfig("ignoreSSL")},
                {"httpProxy", this._kernel.GetConfig("httpProxy")},
                {"connectTimeout", 15000},
                {"readTimeout", 15000},
                {"retry", new Dictionary<string, int?>
                {
                    {"maxAttempts", 0},
                }},
            };

            TeaRequest _lastRequest = null;
            Exception _lastException = null;
            long _now = System.DateTime.Now.Millisecond;
            int _retryTimes = 0;
            while (TeaCore.AllowRetry((IDictionary) runtime_["retry"], _retryTimes, _now))
            {
                if (_retryTimes > 0)
                {
                    int backoffTime = TeaCore.GetBackoffTime((IDictionary)runtime_["backoff"], _retryTimes);
                    if (backoffTime > 0)
                    {
                        TeaCore.Sleep(backoffTime);
                    }
                }
                _retryTimes = _retryTimes + 1;
                try
                {
                    TeaRequest request_ = new TeaRequest();
                    Dictionary<string, string> systemParams = new Dictionary<string, string>
                    {
                        {"method", "alipay.trade.precreate"},
                        {"app_id", this._kernel.GetConfig("appId")},
                        {"timestamp", this._kernel.GetTimestamp()},
                        {"format", "json"},
                        {"version", "1.0"},
                        {"alipay_sdk", this._kernel.GetSdkVersion()},
                        {"charset", "UTF-8"},
                        {"sign_type", this._kernel.GetConfig("signType")},
                        {"app_cert_sn", this._kernel.GetMerchantCertSN()},
                        {"alipay_root_cert_sn", this._kernel.GetAlipayRootCertSN()},
                    };
                    Dictionary<string, object> bizParams = new Dictionary<string, object>
                    {
                        {"subject", subject},
                        {"out_trade_no", outTradeNo},
                        {"total_amount", totalAmount},
                    };
                    Dictionary<string, string> textParams = new Dictionary<string, string>(){};
                    request_.Protocol = this._kernel.GetConfig("protocol");
                    request_.Method = "POST";
                    request_.Pathname = "/gateway.do";
                    request_.Headers = new Dictionary<string, string>
                    {
                        {"host", this._kernel.GetConfig("gatewayHost")},
                        {"content-type", "application/x-www-form-urlencoded;charset=utf-8"},
                    };
                    request_.Query = this._kernel.SortMap(TeaConverter.merge<string>
                    (
                        new Dictionary<string, string>()
                        {
                            {"sign", this._kernel.Sign(systemParams, bizParams, textParams, this._kernel.GetConfig("merchantPrivateKey"))},
                        },
                        systemParams,
                        textParams
                    ));
                    request_.Body = TeaCore.BytesReadable(this._kernel.ToUrlEncodedRequestBody(bizParams));
                    _lastRequest = request_;
                    TeaResponse response_ = await TeaCore.DoActionAsync(request_, runtime_);

                    Dictionary<string, object> respMap = await this._kernel.ReadAsJsonAsync(response_, "alipay.trade.precreate");
                    if (this._kernel.IsCertMode())
                    {
                        if (this._kernel.Verify(respMap, this._kernel.ExtractAlipayPublicKey(this._kernel.GetAlipayCertSN(respMap))))
                        {
                            return TeaModel.ToObject<AlipayTradePrecreateResponse>(this._kernel.ToRespModel(respMap));
                        }
                    }
                    else
                    {
                        if (this._kernel.Verify(respMap, this._kernel.GetConfig("alipayPublicKey")))
                        {
                            return TeaModel.ToObject<AlipayTradePrecreateResponse>(this._kernel.ToRespModel(respMap));
                        }
                    }
                    throw new TeaException(new Dictionary<string, string>
                    {
                        {"message", "驗籤失敗，請檢查支付寶公鑰設定是否正確。"},
                    });
                }
                catch (Exception e)
                {
                    if (TeaCore.IsRetryable(e))
                    {
                        _lastException = e;
                        continue;
                    }
                    throw e;
                }
            }

            throw new TeaUnretryableException(_lastRequest, _lastException);
        }

        
        /// <summary>
        /// ISV代商戶代用，指定appAuthToken
        /// </summary>
        /// <param name="appAuthToken">代呼叫token</param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client Agent(string appAuthToken)
        {
            _kernel.InjectTextParam("app_auth_token", appAuthToken);
            return this;
        }

        /// <summary>
        /// 使用者授權呼叫，指定authToken
        /// </summary>
        /// <param name="authToken">使用者授權token</param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client Auth(string authToken)
        {
            _kernel.InjectTextParam("auth_token", authToken);
            return this;
        }

        /// <summary>
        /// 設定非同步通知回撥地址，此處設定將在本呼叫中覆蓋Config中的全域性配置
        /// </summary>
        /// <param name="url">非同步通知回撥地址，例如：https://www.test.com/callback </param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client AsyncNotify(string url)
        {
            _kernel.InjectTextParam("notify_url", url);
            return this;
        }

        /// <summary>
        /// 將本次呼叫強制路由到後端系統的測試地址上，常用於線下環境內外聯調，沙箱與線上環境設定無效
        /// </summary>
        /// <param name="testUrl">後端系統測試地址</param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client Route(string testUrl)
        {
            _kernel.InjectTextParam("ws_service_url", testUrl);
            return this;
        }

        /// <summary>
        /// 設定API入參中沒有的其他可選業務請求引數(biz_content下的欄位)
        /// </summary>
        /// <param name="key">業務請求引數名稱（biz_content下的欄位名，比如timeout_express）</param>
        /// <param name="value">
        /// 業務請求引數的值，一個可以序列化成JSON的物件
        /// 如果該欄位是一個字串型別（String、Price、Date在SDK中都是字串），請使用string儲存
        /// 如果該欄位是一個數值型型別（比如：Number），請使用long儲存
        /// 如果該欄位是一個複雜型別，請使用巢狀的Dictionary指定各下級欄位的值
        /// 如果該欄位是一個數組，請使用List儲存各個值
        /// 對於更復雜的情況，也支援Dictionary和List的各種組合巢狀，比如引數是值是個List，List中的每種型別是一個複雜物件
        /// </param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client Optional(string key, object value)
        {
            _kernel.InjectBizParam(key, value);
            return this;
        }

        /// <summary>
        /// 批次設定API入參中沒有的其他可選業務請求引數(biz_content下的欄位)
        /// optional方法的批次版本
        /// </summary>
        /// <param name="optionalArgs">可選引數集合，每個引數由key和value組成，key和value的格式請參見optional方法的註釋</param>
        /// <returns>本客戶端，便於鏈式呼叫</returns>
        public Client BatchOptional(Dictionary<string, object> optionalArgs)
        {
            foreach (var pair in optionalArgs)
            {
                _kernel.InjectBizParam(pair.Key, pair.Value);
            }
            return this;
        }
    }
}