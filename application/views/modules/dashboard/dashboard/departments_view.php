<div style="max-height: 500px; overflow-y: auto;">
                           <table class="table table-striped hover" align=center" width="100%">
                    <thead>
                      <tr>
                        <td align="left" style="width: 70%;border-left:1px solid #EAF2F8"><span style="margin-left: 10px;">BRANCH CODE - DEPARTMENT</span></td>
                        <td align="center" style="border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8">STAFF</td>
                      </tr>
                    </thead>
                    <?php 
                    
                    foreach ($departments as $dep) {
                      ?>
                      <tr>
                        <td align="left" style="border-left:1px solid #EAF2F8" >
                          <?php echo $dep->branchCode.' - '.$dep->deptName?>
                        </td>
                        <td align="center" style="width:100px; border-left:1px solid #EAF2F8; border-right:1px solid #EAF2F8">
                          <?php 
                          $this->db->where('deptID',$dep->deptID);
                          $this->db->where('status',1);
                          echo $rows = $this->db->count_all_results('employments');
                          $ttl += $rows;
                          ?>
                        </td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <td align="left" style="border-left:1px solid #EAF2F8">TOTAL EMPLOYEES : </td>
                      <td align="center" style="border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8">
                        <?php echo $ttl;?>
                      </td>
                    </tr>
                  </table>
                </div>