
export function elementUiDelete(that){
    that.$confirm('此操作将永久删除, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        that.$message({
          type: 'success',
          message: '删除成功!'
        });
      }).catch(() => {
        that.$message({
          type: 'info',
          message: '已取消删除'
        });          
      });
}