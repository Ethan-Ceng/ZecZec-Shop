// This file is auto-generated, don't edit it. Thanks.

using System;
using System.Collections.Generic;
using System.IO;

using Tea;

namespace Alipay.EasySDK.Payment.Wap.Models
{
    public class AlipayTradeWapPayResponse : TeaModel {
        /// <summary>
        /// 訂單資訊，Form表單形式
        /// </summary>
        [NameInMap("body")]
        [Validation(Required=true)]
        public string Body { get; set; }

    }

}
