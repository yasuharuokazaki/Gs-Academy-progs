
function decimalToBinary(decNumber){
    let bitNumber=[];
    toBinary(decNumber,bitNumber)
    console.log(bitNumber.join(''));
}


function toBinary(n,array){
    if(n !== 0){
        while(n>0){
            array.unshift(n%2);
            nextDecNum = Math.floor(n/2);
            return toBinary(nextDecNum,array);
        }
    }
}