using NUnit.Framework;
using Alipay.EasySDK.Factory;
using Alipay.EasySDK.Marketing.OpenLife.Models;
using System.Collections.Generic;
using Alipay.EasySDK.Kernel.Util;

namespace UnitTest.Marketing.OpenLife
{
    public class ClientTest
    {
        [SetUp]
        public void SetUp()
        {
            Factory.SetOptions(TestAccount.OpenLife.CONFIG);
        }

        [Test]
        public void TestCreateImageTextContent()
        {
            AlipayOpenPublicMessageContentCreateResponse response = Factory.Marketing.OpenLife().CreateImageTextContent("標題",
                    "http://dl.django.t.taobao.com/rest/1.0/image?fileIds=hOTQ1lT1TtOjcxGflvnUXgAAACMAAQED",
                    "示例", "T", "activity", "滿100減10",
                    "關鍵,熱度", "13434343432,xxx@163.com");

            Assert.IsTrue(ResponseChecker.Success(response));
            Assert.AreEqual(response.Code, "10000");
            Assert.AreEqual(response.Msg, "Success");
            Assert.Null(response.SubCode);
            Assert.Null(response.SubMsg);
            Assert.NotNull(response.HttpBody);
            Assert.NotNull(response.ContentId);
            Assert.NotNull(response.ContentUrl);
        }

        [Test]
        public void TestModifyImageTextContent()
        {
            AlipayOpenPublicMessageContentModifyResponse response = Factory.Marketing.OpenLife().ModifyImageTextContent(
                    "20190510645210035577f788-d6cd-4020-9dba-1a195edb7342", "新標題",
                    "http://dl.django.t.taobao.com/rest/1.0/image?fileIds=hOTQ1lT1TtOjcxGflvnUXgAAACMAAQED",
                    "新示例", "T", "activity", "滿100減20",
                    "關鍵,熱度", "13434343432,xxx@163.com");

            Assert.IsTrue(ResponseChecker.Success(response));
            Assert.AreEqual(response.Code, "10000");
            Assert.AreEqual(response.Msg, "Success");
            Assert.Null(response.SubCode);
            Assert.Null(response.SubMsg);
            Assert.NotNull(response.HttpBody);
            Assert.AreEqual(response.ContentId, "20190510645210035577f788-d6cd-4020-9dba-1a195edb7342");
            Assert.NotNull(response.ContentUrl);
        }

        [Test]
        public void TestSendText()
        {
            AlipayOpenPublicMessageTotalSendResponse response = Factory.Marketing.OpenLife().SendText("測試");

            if (response.Code.Equals("10000"))
            {
                Assert.IsTrue(ResponseChecker.Success(response));
                Assert.AreEqual(response.Code, "10000");
                Assert.AreEqual(response.Msg, "Success");
                Assert.Null(response.SubCode);
                Assert.Null(response.SubMsg);
                Assert.NotNull(response.HttpBody);
                Assert.NotNull(response.MessageId);
            }
            else
            {
                Assert.IsFalse(ResponseChecker.Success(response));
                Assert.AreEqual(response.Code, "40004");
                Assert.AreEqual(response.Msg, "Business Failed");
                Assert.AreEqual(response.SubCode, "PUB.MSG_BATCH_SD_OVER");
                Assert.AreEqual(response.SubMsg, "批次傳送訊息頻率超限");
                Assert.NotNull(response.HttpBody);
                Assert.Null(response.MessageId);
            }
        }

        [Test]
        public void TestSendImageText()
        {
            Article article = new Article
            {
                ActionName = "測試",
                Desc = "測試",
                Title = "測試",
                ImageUrl = "http://dl.django.t.taobao.com/rest/1.0/image?fileIds=hOTQ1lT1TtOjcxGflvnUXgAAACMAAQED",
                Url = "https://docs.open.alipay.com/api_6/alipay.open.public.message.total.send"
            };
            AlipayOpenPublicMessageTotalSendResponse response = Factory.Marketing.OpenLife().SendImageText(new List<Article> { article });

            if (response.Code.Equals("10000"))
            {
                Assert.IsTrue(ResponseChecker.Success(response));
                Assert.AreEqual(response.Code, "10000");
                Assert.AreEqual(response.Msg, "Success");
                Assert.Null(response.SubCode);
                Assert.Null(response.SubMsg);
                Assert.NotNull(response.HttpBody);
                Assert.NotNull(response.MessageId);
            }
            else
            {
                Assert.IsFalse(ResponseChecker.Success(response));
                Assert.AreEqual(response.Code, "40004");
                Assert.AreEqual(response.Msg, "Business Failed");
                Assert.AreEqual(response.SubCode, "PUB.MSG_BATCH_SD_OVER");
                Assert.AreEqual(response.SubMsg, "批次傳送訊息頻率超限");
                Assert.NotNull(response.HttpBody);
                Assert.Null(response.MessageId);
            }
        }

        [Test]
        public void TestSendSingleMessage()
        {
            Keyword keyword = new Keyword
            {
                Color = "#85be53",
                Value = "HU7142"
            };
            Context context = new Context
            {
                HeadColor = "#85be53",
                Url = "https://docs.open.alipay.com/api_6/alipay.open.public.message.single.send",
                ActionName = "檢視詳情",
                Keyword1 = keyword,
                Keyword2 = keyword,
                First = keyword,
                Remark = keyword
            };
            Alipay.EasySDK.Marketing.OpenLife.Models.Template template = new Alipay.EasySDK.Marketing.OpenLife.Models.Template
            {
                TemplateId = "e44cd3e52ffa46b1a50afc145f55d1ea",
                Context = context
            };
            AlipayOpenPublicMessageSingleSendResponse response = Factory.Marketing.OpenLife().SendSingleMessage(
                    "2088002656718920", template);

            Assert.IsTrue(ResponseChecker.Success(response));
            Assert.AreEqual(response.Code, "10000");
            Assert.AreEqual(response.Msg, "Success");
            Assert.IsNull(response.SubCode);
            Assert.IsNull(response.SubMsg);
            Assert.NotNull(response.HttpBody);
        }

        [Test]
        public void TestRecallMessage()
        {
            AlipayOpenPublicLifeMsgRecallResponse response = Factory.Marketing.OpenLife().RecallMessage("201905106452100327f456f6-8dd2-4a06-8b0e-ec8a3a85c46a");

            Assert.IsTrue(ResponseChecker.Success(response));
            Assert.AreEqual(response.Code, "10000");
            Assert.AreEqual(response.Msg, "Success");
            Assert.IsNull(response.SubCode);
            Assert.IsNull(response.SubMsg);
            Assert.NotNull(response.HttpBody);
        }

        [Test]
        public void TestSetIndustry()
        {
            AlipayOpenPublicTemplateMessageIndustryModifyResponse response = Factory.Marketing.OpenLife().SetIndustry(
                "10001/20102", "IT科技/IT軟體與服務",
                    "10001/20102", "IT科技/IT軟體與服務");

            if (response.Code.Equals("10000"))
            {
                Assert.IsTrue(ResponseChecker.Success(response));
                Assert.AreEqual(response.Code, "10000");
                Assert.AreEqual(response.Msg, "Success");
                Assert.Null(response.SubCode);
                Assert.Null(response.SubMsg);
                Assert.NotNull(response.HttpBody);
            }
            else
            {
                Assert.IsFalse(ResponseChecker.Success(response));
                Assert.AreEqual(response.Code, "40004");
                Assert.AreEqual(response.Msg, "Business Failed");
                Assert.AreEqual(response.SubCode, "3002");
                Assert.AreEqual(response.SubMsg, ("模板訊息行業一月只能修改一次"));
                Assert.NotNull(response.HttpBody);
            }
        }

        [Test]
        public void TestGetIndustry()
        {
            AlipayOpenPublicSettingCategoryQueryResponse response = Factory.Marketing.OpenLife().GetIndustry();

            Assert.IsTrue(ResponseChecker.Success(response));
            Assert.AreEqual(response.Code, "10000");
            Assert.AreEqual(response.Msg, "Success");
            Assert.Null(response.SubCode);
            Assert.Null(response.SubMsg);
            Assert.NotNull(response.HttpBody);
            Assert.AreEqual(response.PrimaryCategory, "IT科技/IT軟體與服務");
            Assert.AreEqual(response.SecondaryCategory, "IT科技/IT軟體與服務");
        }
    }
}
