import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity } from 'react-native'
import React from 'react'
import { MaterialIcons } from '@expo/vector-icons';


const Esqueceuasenhacodigo = ({ navigation, route }) => {
    const { useremail, userVerificationCode } = route.params;
    console.log(useremail, userVerificationCode)

    const [verificationCode, setVerificationCode] = React.useState('');


    const handleVerificationCode = () => {

        if (verificationCode != userVerificationCode) {
           
        }
        else {
           
            navigation.navigate('Esqueceuasenhaescolher', { email: useremail })
        }

        
    }
    return (
        <View style={{width: '100%', height: '100%'}}>

             <View style={{flexDirection: 'row', marginTop: 20}}>

            <Text style={{marginStart: 20, fontSize: 20, fontWeight: 'bold'}}>Ajuda para login</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Login')} style={{marginStart: 185,
            marginTop: 10
            }}>

            <Image source={require('../../images/saircards.png')} style={{
            width: 15, height: 15
            }}/>

            </TouchableOpacity>

            </View>

      
            <Text style={{fontWeight: 'bold', marginTop: 40, alignSelf: 'center',
        fontSize: 15, width: 350, textAlign: 'center', color: 'gray'}} 
        >Verifique o código que foi enviado para seu email</Text>

            <TextInput placeholder="Entre com o código de 6 dígitos aqui" style={{width: '95%', backgroundColor: 'white', height: 40, alignSelf: 'center',
            marginTop: 30, borderRadius: 5, padding: 10}}
                onChangeText={(text) => setVerificationCode(text)}
            />

            <TouchableOpacity style={{backgroundColor: '#ec230d', width: '95%', 
                justifyContent: 'center', alignItems: 'center', alignSelf: 'center', marginTop: 10,
                height: 40, borderRadius: 5}} onPress={() => handleVerificationCode()}>

                    <Text style={{color: 'white', fontWeight: 'bold'}}>
                        Avançar
                    </Text>
                    </TouchableOpacity>
        </View>
    )
}



export default Esqueceuasenhacodigo

const styles = StyleSheet.create({})