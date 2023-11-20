import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, StatusBar } from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';

const Registrarverificaremail = ({ navigation, route }) => {
    const { useremail, userVerificationCode } = route.params
    console.log(useremail, userVerificationCode)


    const [verificationCode, setVerificationCode] = useState('')

    const handleVerificationCode = () => {
        if (verificationCode != userVerificationCode) {
            alert('Código de verificação invalído')
        }
        else if (verificationCode == userVerificationCode) {
            navigation.navigate('Registrarusername', { email: useremail })
        }
        else {
            
        }

       
    }


    return (
        <View style={{ width: '100%', height: '100%'}}>
        <StatusBar hidden={true}/>
       <TouchableOpacity onPress={() => navigation.navigate('Login')} >

          <Image style={{alignSelf: 'flex-end', marginTop: 20, marginEnd: 20, width: 20, height: 20}} source={require('../../images/saircards.png')}/>

       </TouchableOpacity>
         
       <View style={{width: '100%', justifyContent: 'center', alignItems: 'center', marginTop: 50}}>
            
            <Text  style={{fontSize: 25, fontWeight: 'bold'}}>Verique seu email</Text>
            
            <Text style={{ textAlign: 'left', fontSize: 14, marginTop: 10, width: 250}}>
                O código para a verificação da conta foi{'\n'}enviado para seu email.</Text>
            
            <TextInput style={{width: '95%', height: 40, borderColor: 'gray', borderWidth: 1, borderRadius: 5, marginTop: 20, backgroundColor: 'white',
            padding: 10}}
             placeholder="Entre com o código de 6 dígitos" 

                onChangeText={(text) => setVerificationCode(text)}
            />

<TouchableOpacity  style={{width: '95%', height: 40, borderRadius: 5, marginTop: 20, backgroundColor: '#ec230d',
                    color: 'white', fontSize: 18, alignItems: 'center', justifyContent: 'center'}}  onPress={() => handleVerificationCode()}>
                    <Text style={{fontSize: 15, color: 'white', fontWeight: 'bold'}}
                    
                    >
                        Avançar
                    </Text>
                    </TouchableOpacity>
            </View>
        </View>
    )
}



export default Registrarverificaremail

const styles = StyleSheet.create({})