//压缩图片
function convertImgToBase64(file, callback){
    var url = null ;
    if (window.createObjectURL!=undefined) {  // basic
        url = window.createObjectURL(file) ;
    } else if (window.URL!=undefined) {       // mozilla(firefox)
        url = window.URL.createObjectURL(file) ;
    } else if (window.webkitURL!=undefined) { // web_kit or chrome
        url = window.webkitURL.createObjectURL(file) ;
    }

    var img = new Image;
    img.src = url;
    img.crossOrigin = 'Anonymous';
    img.onload = function(){
        //压缩的大小
        var max_width =1024;
        var max_height =768;

        var canvas = document.createElement('canvas');
        var width = img.width;
        var height = img.height;

        if(width > height) {
            if(width > max_width) {
                height = Math.round(height *= max_width / width);
                width = max_width;
            }
        }
        else{
            if(height > max_height) {
                width = Math.round(width *= max_height / height);
                height = max_height;
            }
        }

        canvas.width = width;
        canvas.height = height;

        var ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0, width, height);
        //压缩率
        var dataURL =  canvas.toDataURL("image/jpeg",1);

        callback.call(this, dataURL);
        canvas = null;
    };
}