if($rw[3]=='month'){
            $sql=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='$loan_id' AND pay_date >= '".date('Y-m-d',strtotime($final))."' AND pay_date < '".date('Y-m-d',strtotime($loop_date))."'  ");
            if(mysqli_num_rows($sql)){
              while($rws = mysqli_fetch_array($sql)){
                $pay = $rws[1];
                if($i==0){ 
                   $actual_interest = $loan*($int/100);                                    
                   $bal =  $loan-($rws[1]-$actual_interest);  
                   if($actual_interest>$rws[1]){
                    $bal = $loan;
                    $acc_int = $actual_interest-$rws[1];
                   }                 
                  }else{
                    $actual_interest = $bal*($int/100);
                    $acc_int += $actual_interest;
                   if($rws[1]==($acc_int)){
                     
                     $acc_int=0;
                     $bal = $bal;
                   }else if($rws[1]>$acc_int){                                       
                     
                     $bal = ($bal+$acc_int)-$rws[1];
                     $acc_int=0;                                          
                   }else if($rws[1]<$acc_int){
                      $acc_int = $acc_int-$rws[1];                      
                    }                   
                  }
                 $total += $rws[1];  
                
              ?>
              <tr>
                <td><?php echo $count ?></td>
                <td><?php echo $final ?></td>
                <td></td>
                <td><?php echo date("d-m-Y",strtotime($rws[2])) ?></td>
                <td align="right"><?php echo number_format($rws[1]) ?></td>
                <td align="right"><?php echo number_format($acc_int) ?></td>
                <td align="right"><?php echo number_format($acc_int) ?></td>
                <td align="right"><?php echo number_format($bal) ?></td>
                <td></td>
              </tr>
              <?php  
                 if($bal<=0){
                    $loan=0;
                    $interest=0;
                    $acc_int=0;
                 }
                            
              }
            }else{              
              if($bal>0){
                $interest = $bal * ($int/100);
              }else{
                $interest = $loan * ($int/100);
                $bal=$loan;
              } 
              $acc_int += $interest;    
            ?>
             <tr>
                <td><?php echo $count ?></td>
                <td><?php echo $final ?></td>
                <td>-</td>
                <td>-</td>
                <td align="right"></td>
                <td align="right"><?php echo number_format($acc_int) ?></td>
                <td align="right"><?php echo number_format($pmt) ?></td>
                <td align="right"><?php echo number_format($bal) ?></td>
                <td align="right"><?php echo number_format($bal) ?></td>
              </tr>
            <?php
        }

  } else