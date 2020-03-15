
export function isSuper(that){
    if(that.$store.state.role_id !== "1"){
        return that.$message.error("你没有权限进行此操作")
    }
}