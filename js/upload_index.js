$(document).ready(function(){
			var fileurl=$("#url")[0];//返回javascript原始对象 若没有[0]则会返回jquery对象
			var upload=$("#up");
			var files_box=$("#files_box_bg #files_box .file .file_up");
			$("#files_box_bg").hide();
			var drop=new dorpFile(document,function(data){
				if(data.length<=0)return;//排除拖拽其他内容情况
				filesList=null;
				if(document.all){//for ie
					printfFileDetail(data);
				}else{
					fileurl.files=data;//这样赋值给选择框ie不触发onchange事件
				}
			});
			
			fileurl.onchange=function(){
				//alert(fileurl.files);
				filesList=null;
				printfFileDetail(fileurl.files);
				
			};
			
			$("#files_box_bg .files_box_tou .check_detail_bg .checkAll_b").click(function(){
				checkAll();
			});
			$("#files_box_bg .files_box_foot .check_detail_bg .checkAll_b").click(function(){
				checkAll();
			});
});
		var filesMd5=new Array();
		var filesList=null;
		function printfFileDetail(data){
			filesList=data;//赋值文件数组
			var i,str="";
			str+='<div class="file">';
					str+='<div class="file_li">序号</div>';
					str+='<div class="file_name">名字</div>';
					str+='<div class="file_size">大小</div>';
					str+='<div class="file_type">类型</div>';
					str+='<div class="file_md5">MD5</div>';
					str+='<div class="file_upprogress">上传进度</div>';
					str+='<div class="file_check">选择</div>';
					str+='<div class="file_up">状态</div>';
			str+='</div>';
			for(i=0;i<data.length;i++){
				str+='<div class="file">';
					str+='<div class="file_li">'+(i+1)+'.</div>';
					str+='<div class="file_name" style="color:#BBBABA"><span class="title"></span>'+data[i].name+'</div>';
					str+='<div class="file_size"><span class="title"></span>'+toSize(data[i].size)+'</div>';
					str+='<div class="file_type"><span class="title"></span>'+getFileHouzhui(data[i].name)+'</div>';
					str+='<div class="file_md5"><span class="title"></span><div class="md5_b">获取md5</div></div>';
					str+='<div class="file_upprogress">等待上传</div>';
					str+='<div class="file_check"><input type="checkbox" ></div>';
					str+='<div class="file_up"><input type="button" value="上传"></div>';
				str+='</div>';
			}
			$("#upload_bg").animate({
				height:150,
				
			});
			$("#form").animate({
				"margin-top":"50",
			});
			$("#files_box_bg #files_box").html(str);
			$("#files_box_bg").hide();
			$("#files_box_bg").slideDown();
			
			//$("#files_box_bg #files_box .file").slideUp(0);
			//$("#files_box_bg #files_box .file").fadeIn(1000);
			//更新已经选择文件个数
			$("#files_box_bg #files_box .file .file_check input").click(function(){
				var check=$("#files_box_bg #files_box .file .file_check input");
				var num=0;
				for(var i=0;i<check.length;i++){
					if(check[i].checked==true)
						num++;
				}
				$("#files_box_bg .files_box_tou .check_detail_bg .check_detail").html("共选择"+num+"个");
			});
			//初始化filesMd5
			for(var i=0;i<data.length;i++){
				filesMd5[i]=null;
			}
			//手动计算MD5
			$("#files_box_bg #files_box .file .file_md5 .md5_b").click(function(){
				getMd5($(this));
			});
			//手动单文件上传
			$("#files_box_bg #files_box .file .file_up input").click(function(){
				//alert($(this).parent().parent().index());
				// var md5=filesMd5[$(this).parent().parent().index()-1];
				/*if(md5==undefined||md5==null||md5==""){
					alert("计算中...");
				}else{
					alert(md5);
					
				}*/
				upload($(this).parent().parent().index()-1,false);
				//console.log($(this).parent().parent().index()-1);
				
			});
			//全部上传
			$("#files_box_bg .files_box_tou .check_detail_bg #uploadAll_b1").click(function(){
				$("#files_box_bg #files_box .file .file_up").not(":eq(0)").html("等待中");
				//console.log(filesList.length);
				upload(0,true);
			});
			$("#files_box_bg .files_box_foot .check_detail_bg #uploadAll_b2").click(function(){
				//console.log(filesList);
				var ajax=new XMLHttpRequest();
				for(var v in ajax){
					console.log(v);
				}
			});
		}
		var chck=1;
		function checkAll(){
			var check=$("#files_box_bg #files_box .file .file_check input");
			if(chck==1){
				chck=0;
				for(var i=0;i<check.length;i++){
					check[i].checked=true;
				}

				$("#files_box_bg .files_box_tou .check_detail_bg .checkAll_b input")[0].checked=true;
				$("#files_box_bg .files_box_foot .check_detail_bg .checkAll_b input")[0].checked=true;
				$("#files_box_bg .files_box_tou .check_detail_bg .check_detail").html("共选择"+i+"个");
				$("#files_box_bg .files_box_foot .check_detail_bg .check_detail").html("共选择"+i+"个");
			}else{
				chck=1;
				for(var i=0;i<check.length;i++){
					check[i].checked=false;
				}

				$("#files_box_bg .files_box_tou .check_detail_bg .checkAll_b input")[0].checked=false;
				$("#files_box_bg .files_box_foot .check_detail_bg .checkAll_b input")[0].checked=false;
				$("#files_box_bg .files_box_tou .check_detail_bg .check_detail").html("共选择"+0+"个");
				$("#files_box_bg .files_box_foot .check_detail_bg .check_detail").html("共选择"+0+"个");
			}
		}

		//上传文件
		function upload(fileNum,bool){//bool=true时自动上传下一文件
			var c="<span style='color:#057E2E;font-size:14px;'>完成</span>";
			var pstr='<progress min="0" max="100" value="0" ></progress>';
			if(filesList[fileNum].size>=300*Math.pow(2,20)){//断点上传 >=250MB
				var str=new whitting();
				str.start("断点中...",$("#files_box_bg #files_box .file .file_up").eq(fileNum+1)[0]);
				$("#files_box_bg #files_box .file .file_upprogress").eq(fileNum+1).html(pstr);
				uploadSlice(filesList[fileNum],function(){
					console.log("完成！");
					str.end();
					$("#files_box_bg #files_box .file .file_up").eq(fileNum+1).html(c);
					if(fileNum<filesList.length-1&&bool==true){
						fileNum++;
						upload(fileNum,true);
						//console.log(fileNum);
					}
				},function(e,t){
					$("#files_box_bg #files_box .file .file_upprogress").eq(fileNum+1).children("progress").val(e/t*100);
				});
			}else{//整个文件上传
				var file=new FormData();
				file.append("wen",filesList[fileNum]);
				var xhr=new XMLHttpRequest();
				xhr.open("POST","./up.php","true");
				xhr.onloadstart=function(){
					
					$("#files_box_bg #files_box .file .file_upprogress").eq(fileNum+1).html(pstr);
					//console.log(fileNum);
				}
				xhr.onload=function(){
					//console.log(xhr.responseText);
				}
				xhr.onloadend=function(){
					//console.log(xhr.responseText);
					
					$("#files_box_bg #files_box .file .file_up").eq(fileNum+1).html(c);
					if(fileNum<filesList.length-1&&bool==true){
						fileNum++;
						upload(fileNum,true);
						//console.log(fileNum);
					}
				}
				xhr.upload.onprogress=function(e){
					//console.log($("#files_box_bg #files_box .file .file_upprogress").eq(fileNum+1).val());
					$("#files_box_bg #files_box .file .file_upprogress").eq(fileNum+1).children("progress").val(e.loaded/e.totalSize*100);
				}
				setTimeout(function(){
					xhr.abort();
				},2000);
				xhr.onabort=function(){
					console.log("xhr停止！");
				}
				xhr.send(file);
			}
			
		}
		/*
		*	文件断点上传 function
		*	uploadSlice(file,callback);//完成时回调callback函数
		*	copyright by bluemoon
		*	1752295326@qq.com
		*	2015-2-21 22:42
		*/
		function uploadSlice(file,callback1,callback2){
			var sliceSize=2*Math.pow(2,20);//2MB
			var sliceTotal=Math.ceil(file.size/sliceSize);
			//var blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice;
			var nowSliceNum=0;
			uploadBlob();
			function uploadBlob(){
				var sliceData=new FormData();
				var xhr=new XMLHttpRequest();
				var start=nowSliceNum*sliceSize;
				var end=((start+sliceSize)>=file.size)?file.size:(start+sliceSize);
				var fileSliceData=file.slice(start,end);
				sliceData.append("wen",fileSliceData);
				sliceData.append("sliceTotal",sliceTotal);
				sliceData.append("blodNum",nowSliceNum);
				sliceData.append("fileName",file.name);
				fileMd5(fileSliceData,function(m){//取得片MD5
					sliceData.append("fileMd5",m);
					xhr.open("POST","./uploadSlice.php","true");
					xhr.onloadend=function(){
						console.log(nowSliceNum,sliceTotal);
						callback2(nowSliceNum,sliceTotal-1);
						if(nowSliceNum<sliceTotal-1){
							nowSliceNum++;
							uploadBlob();
						}else{
							callback1();
						}
					}

					xhr.send(sliceData);
				})
			}
		}
		function getFileHouzhui(url){
			var str=url.split(/\./);
			var type="";
			if(str.length>=3){
				type=str[str.length-2]+"."+str[str.length-1];
			}else{
				type=str[str.length-1];
			}
			return type;
		}


		function getMd5(obj){
				var thisfile=obj.parent().parent();
				var thismd5=obj.parent();
				var s=new whitting();
				s.start("计算中...",thismd5[0]);
				
				//thismd5.html("计算中...");
				fileMd5(filesList[thisfile.index()-1],function(md5){//计算成功时
					s.end();
					thismd5.html(md5);
					filesMd5[thisfile.index()-1]=md5;
				});	
		}

		/*
		*	
		*	new dorpFile(obj,calbackfunction);
		*	copyright by bluemoon
		*	1752295326@qq.com
		*	2015-1-9 11:56
		*/
		function dorpFile(obj,callback){
				this.obj=obj;
				this.obj.addEventListener("dragover",function(event){//
				event.stopPropagation();
				event.preventDefault();
				/*
				*preventDefault()	
				*stopPropagation()	
				*/
				},false);
				
				this.obj.addEventListener("drop",function(event){//
					event.stopPropagation();
					event.preventDefault();

					callback(event.dataTransfer.files);//
				},false);
		}
		function toSize(data){
			var size=data;
			var dw="Byte";
			if(data>Math.pow(2,30)){
				size=data/Math.pow(2,30);
				dw="GB";
			}else if(data>Math.pow(2,20)){
				size=data/Math.pow(2,20);
				dw="MB";
			}else if(data>Math.pow(2,10)){
				size=data/Math.pow(2,10);
				dw="KB";
			}
			return size.toFixed(2)+dw;
		}
		/*
		*	Â»Ã±ÃˆÂ¡ÃŽÃ„Â¼Ã¾md5
		*	fileMd5(fileobj,callbackFunction);
		*	copyright by bluemoon
		*	1752295326@qq.com
		*	2015-1-9 11:56
		*/
		function fileMd5(fileObj,callback){
            var blobSlice=File.prototype.slice||File.prototype.mozSlice||File.prototype.webkitSlice;
            var spark=new SparkMD5.ArrayBuffer();//
            var start=0;
            var end=fileObj.size;
            var md5="";
            var fileReader=new FileReader();//fileAPI
            fileReader.readAsArrayBuffer(blobSlice.call(fileObj,start,end));//
            fileReader.onload= function(e){//Â¼Ã†Ã‹Ã£ÃÃªÂ³Ã‰Ã—Ã”Â¶Â¯ÂµÃ·Ã“ÃƒÂºÂ¯ÃŠÃ½
            	spark.append(e.target.result);
                md5=spark.end();//Â·ÂµÂ»Ã˜Â¼Ã†Ã‹Ã£Â½Ã¡Â¹Ã»
               	callback(md5);//ÂµÃ·Ã“ÃƒÂ»Ã˜ÂµÃ·ÂºÂ¯ÃŠÃ½
            }
            fileReader.onerror=function(){
                console.warn('oops, something went wrong.');
            };
    	}

    	/*
		*	等待提示 Object
		*	var w=new whitting();w.start(str,obj);w.end();对于jquery对象在后面接"[0]"即可
		*	copyright by bluemoon
		*	1752295326@qq.com
		*	2015-2-20 11:34
		*/
		function whitting(){
			var whittingEfface=null;
			var jishu=3;
			var len=null;
			this.start=function(str,obj){
				len=str.length;
				whittingEfface=setInterval(function(){
					if(jishu<=-1){
						jishu=3;
					}
					//console.log(jishu);
					
					obj.title=str.substring(0,len-jishu);
					obj.innerHTML=str.substring(0,len-jishu);
					jishu--;
				},500)
			};
			this.end=function(){
				whittingEfface=clearInterval(whittingEfface);
			}
		}
		/*
		*	代码执行时间计算 Object
		*	var t=new codeTime();t.start();t.end();
		*	copyright by bluemoon
		*	1752295326@qq.com
		*	2015-2-22 11:35
		*/
		function codeTime(){
			var startTime=0;
			var endTime=0;
			this.start=function(){
				startTime=new Date().getTime();
			}
			this.end=function(){
				endTime=new Date().getTime();
				return to(endTime-startTime);
			}
			function to(d){
				if(d>=1000){
					return (d/1000).toFixed(3)+"s";
				}else{
					return d+"ms";
				}
			}
		}