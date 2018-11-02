function dealError(text='请求超时,请稍候再试！', scene){
    //创建外层div
    const loadingBox=document.createElement('div');
    loadingBox.style.width='100%';
    loadingBox.style.height='100%';
    loadingBox.style.background='rgba(0, 0, 2, 0.6)';
    loadingBox.style.position='fixed';
    loadingBox.style.top='0';
    loadingBox.style.left='0';
    loadingBox.style.overflow='hidden';
    loadingBox.style.zIndex='9999';
    loadingBox.style.display='flex';
    loadingBox.style.alignItems='center';
    loadingBox.style.justifyContent='center';
    //创建外层div子级div
    const loadingContent=document.createElement('div');
    loadingContent.style.width='350px';
    loadingContent.style.height='175px';
    loadingContent.style.background='#fff';
    loadingContent.style.borderRadius='10px';
    loadingBox.style.overflow='hidden';
    loadingBox.appendChild(loadingContent);
    //创建标题p标签
    const loadingText=document.createElement('p');
    loadingText.style.width='330px';
    loadingText.style.height='100px';
    loadingText.style.display='flex';
    loadingText.style.alignItems='center';
    loadingText.style.justifyContent='center';
    loadingText.style.marginTop='10px';
    loadingText.style.fontSize='22px';
    loadingText.style.padding='0 10px';
    loadingText.style.color='#ed0200';
    loadingText.innerHTML=text;
    loadingContent.appendChild(loadingText);
    //创建canvas
    const cav = document.createElement('canvas');
    const ctx = cav.getContext('2d');
    cav.style.width='270px';
    cav.style.height='36px';
    cav.style.marginLeft='36px';
    cav.style.borderRadius='18px';
    loadingContent.appendChild(cav);
    document.body.appendChild(loadingBox);
    let _scene = scene ? scene : '';
    drawProgress(cav,ctx,loadingBox,_scene);
}
//绘制canvas动画
function drawProgress(cav,ctx,loadingBox,_scene){
    let cw = cav.width;
    let ch = cav.height;
    let x = 0;
    function draw() {
        //绘制背景矩形
        ctx.fillStyle = '#ed0200';
        ctx.fillRect(0, 0, cw, ch);
        //绘制进度
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#00d258';
        ctx.moveTo(x, 0);
        ctx.lineTo(x, ch);
    }
    let timer=setInterval(() => {
        if (x <= cw) {
            draw();
            ctx.stroke();
        } else {
            if(loadingBox) document.body.removeChild(loadingBox);
            clearInterval(timer);
            //接口访问失败后刷新当前页
            if(_scene == ''){
                history.back()
            }else{
                location.reload();
            }
            
        }
        x += ctx.lineWidth;
    },25)
}
export default dealError;