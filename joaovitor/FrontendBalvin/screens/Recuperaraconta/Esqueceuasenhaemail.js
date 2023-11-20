import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator } from 'react-native'
import React from 'react'
import { MaterialIcons } from '@expo/vector-icons';

const Esqueceuasenhaemail = ({ navigation }) => {
    const [email, setEmail] = React.useState('')
    const [loading, setLoading] = React.useState(false)


    const handleEmail = () => {
        if (email === '') {
            alert('Please enter email')
        }

        else {
            setLoading(true)
            fetch('http://192.168.0.54:3000/recuperarsenhaemail', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
                .then(res => res.json()).then(data => {
                    if (data.error === "Credenciais inválidas") {
                        // alert('Invalid Credentials')
                        
                        setLoading(false)
                    }
                    else if (data.message === "Código de verificação enviado para o seu email") {
                        setLoading(false)
                       

                        navigation.navigate('Esqueceuasenhacodigo', {
                            useremail: data.email,
                            userVerificationCode: data.VerificationCode
                        })

                    }
                })
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
        fontSize: 20}}>Encontre sua conta</Text>
        
            <TextInput placeholder="Entre com seu email" 
                onChangeText={(text) => setEmail(text)}
                style={{width: '95%', backgroundColor: 'white', height: 40, alignSelf: 'center',
            marginTop: 30, borderRadius: 5, padding: 10}}
            />
            {
                loading ? <ActivityIndicator size="large" color="white" style={{marginTop: 10}} /> :
                <TouchableOpacity style={{backgroundColor: '#ec230d', width: '95%', 
                justifyContent: 'center', alignItems: 'center', alignSelf: 'center', marginTop: 10,
                height: 40, borderRadius: 5}} onPress={() => handleEmail()}>

                    <Text style={{color: 'white', fontWeight: 'bold'}}>
                        Avançar
                    </Text>
                    </TouchableOpacity>
            }
        </View>
    )
}


export default Esqueceuasenhaemail

const styles = StyleSheet.create({})